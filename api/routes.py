from flask import Blueprint, request, jsonify
from models import db, User, Event
from datetime import datetime
import bcrypt
import re

auth_bp = Blueprint('auth', __name__)
event_bp = Blueprint('events', __name__)

def check_password(password, hashed_password):
    # Laravel uses $2y$ prefix for bcrypt. Python's bcrypt requires $2b$ or $2a$.
    # We replace $2y$ with $2b$ to make it compatible.
    if hashed_password.startswith('$2y$'):
        hashed_password = hashed_password.replace('$2y$', '$2b$', 1)
    try:
        return bcrypt.checkpw(password.encode('utf-8'), hashed_password.encode('utf-8'))
    except Exception:
        return False

def parse_date(date_str):
    if not date_str:
        return None
    # Remove trailing Z if present (ISO UTC indicator)
    if date_str.endswith('Z'):
        date_str = date_str[:-1]
    if 'T' in date_str:
        # 2026-09-15T09:00:00 or similar
        return datetime.fromisoformat(date_str)
    else:
        # 2026-09-15 09:00:00
        return datetime.strptime(date_str, '%Y-%m-%d %H:%M:%S')

# --- AUTH ROUTES ---

@auth_bp.route('/auth/login', methods=['POST'])
def login():
    data = request.get_json() or {}
    errors = {}
    
    # Validation
    if 'email' not in data or not data['email']:
        errors['email'] = ['The email field is required.']
    elif not re.match(r"[^@]+@[^@]+\.[^@]+", str(data['email'])):
        errors['email'] = ['The email field must be a valid email address.']
        
    if 'password' not in data or not data['password']:
        errors['password'] = ['The password field is required.']
        
    if errors:
        return jsonify({
            'status': 'error',
            'errors': errors
        }), 422
        
    user = User.query.filter_by(email=data['email']).first()
    
    if not user or not check_password(data['password'], user.password):
        return jsonify({
            'status': 'error',
            'message': 'Invalid credentials'
        }), 401
        
    return jsonify({
        'status': 'success',
        'data': user.to_dict()
    })

@auth_bp.route('/users/<int:id>', methods=['GET'])
def get_user(id):
    user = User.query.get(id)
    if not user:
        return jsonify({
            'status': 'error',
            'message': 'User not found'
        }), 404
        
    return jsonify({
        'status': 'success',
        'data': user.to_dict()
    })

@auth_bp.route('/users/email/<string:email>', methods=['GET'])
def get_user_by_email(email):
    user = User.query.filter_by(email=email).first()
    if not user:
        return jsonify({
            'status': 'error',
            'message': 'User not found'
        }), 404
        
    return jsonify({
        'status': 'success',
        'data': user.to_dict()
    })

# --- EVENT ROUTES ---

@event_bp.route('/events', methods=['GET'])
def index():
    try:
        query = Event.query
        
        status_filter = request.args.get('status')
        if status_filter:
            query = query.filter(Event.status == status_filter)
            
        events = query.order_by(Event.start_date.desc()).all()
        
        return jsonify({
            'status': 'success',
            'data': [e.to_dict() for e in events]
        })
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@event_bp.route('/events/<string:id_or_slug>', methods=['GET'])
def show(id_or_slug):
    try:
        if id_or_slug.isdigit():
            event = Event.query.get(int(id_or_slug))
        else:
            event = Event.query.filter_by(slug=id_or_slug).first()
            
        if not event:
            return jsonify({
                'status': 'error',
                'message': 'Event not found'
            }), 404
            
        return jsonify({
            'status': 'success',
            'data': event.to_dict()
        })
    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@event_bp.route('/events', methods=['POST'])
def store():
    data = request.get_json() or {}
    errors = {}
    
    # Required field validations
    required_fields = ['title', 'slug', 'category', 'venue', 'organizer', 'thumbnail', 'banner', 'start_date', 'end_date']
    for field in required_fields:
        if field not in data or data[field] is None or str(data[field]).strip() == '':
            errors[field] = [f"The {field} field is required."]
            
    # Uniqueness validator
    if 'slug' in data and data['slug']:
        existing = Event.query.filter_by(slug=data['slug']).first()
        if existing:
            errors['slug'] = ["The slug has already been taken."]
            
    # URL validator simple check
    url_pattern = re.compile(r'^https?://')
    for field in ['thumbnail', 'banner']:
        if field in data and data[field] and not url_pattern.match(str(data[field])):
            errors[field] = [f"The {field} field must be a valid URL."]
            
    # Status validator
    if 'status' in data and data['status'] and data['status'] not in ['draft', 'published', 'archived']:
        errors['status'] = ["The selected status is invalid."]
        
    # Date parsing & verification
    start_date = None
    end_date = None
    
    if 'start_date' in data and data['start_date']:
        try:
            start_date = parse_date(data['start_date'])
        except Exception:
            errors['start_date'] = ["The start date is not a valid date."]
            
    if 'end_date' in data and data['end_date']:
        try:
            end_date = parse_date(data['end_date'])
        except Exception:
            errors['end_date'] = ["The end date is not a valid date."]
            
    if start_date and end_date and end_date < start_date:
        errors['end_date'] = ["The end date must be a date after or equal to start date."]
        
    if errors:
        return jsonify({
            'status': 'error',
            'message': 'Validation error',
            'errors': errors
        }), 422
        
    try:
        status_val = data.get('status') or 'draft'
        
        new_event = Event(
            title=data['title'],
            slug=data['slug'],
            category=data['category'],
            venue=data['venue'],
            organizer=data['organizer'],
            description=data.get('description'),
            thumbnail=data['thumbnail'],
            banner=data['banner'],
            start_date=start_date,
            end_date=end_date,
            status=status_val
        )
        
        db.session.add(new_event)
        db.session.commit()
        
        return jsonify({
            'status': 'success',
            'message': 'Event created successfully',
            'data': new_event.to_dict()
        }), 201
    except Exception as e:
        db.session.rollback()
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@event_bp.route('/events/<int:id>', methods=['PUT'])
def update(id):
    event = Event.query.get(id)
    if not event:
        return jsonify({
            'status': 'error',
            'message': 'Event not found'
        }), 404
        
    data = request.get_json() or {}
    errors = {}
    
    # Required field validations
    required_fields = ['title', 'slug', 'category', 'venue', 'organizer', 'thumbnail', 'banner', 'start_date', 'end_date']
    for field in required_fields:
        if field not in data or data[field] is None or str(data[field]).strip() == '':
            errors[field] = [f"The {field} field is required."]
            
    # Uniqueness validator ignoring current record
    if 'slug' in data and data['slug']:
        existing = Event.query.filter(Event.slug == data['slug'], Event.id != id).first()
        if existing:
            errors['slug'] = ["The slug has already been taken."]
            
    # URL validator
    url_pattern = re.compile(r'^https?://')
    for field in ['thumbnail', 'banner']:
        if field in data and data[field] and not url_pattern.match(str(data[field])):
            errors[field] = [f"The {field} field must be a valid URL."]
            
    # Status validator
    if 'status' in data and data['status'] and data['status'] not in ['draft', 'published', 'archived']:
        errors['status'] = ["The selected status is invalid."]
        
    # Date parsing & verification
    start_date = None
    end_date = None
    
    if 'start_date' in data and data['start_date']:
        try:
            start_date = parse_date(data['start_date'])
        except Exception:
            errors['start_date'] = ["The start date is not a valid date."]
            
    if 'end_date' in data and data['end_date']:
        try:
            end_date = parse_date(data['end_date'])
        except Exception:
            errors['end_date'] = ["The end date is not a valid date."]
            
    if start_date and end_date and end_date < start_date:
        errors['end_date'] = ["The end date must be a date after or equal to start date."]
        
    if errors:
        return jsonify({
            'status': 'error',
            'message': 'Validation error',
            'errors': errors
        }), 422
        
    try:
        event.title = data['title']
        event.slug = data['slug']
        event.category = data['category']
        event.venue = data['venue']
        event.organizer = data['organizer']
        event.description = data.get('description')
        event.thumbnail = data['thumbnail']
        event.banner = data['banner']
        event.start_date = start_date
        event.end_date = end_date
        if 'status' in data:
            event.status = data['status'] or 'draft'
            
        db.session.commit()
        
        return jsonify({
            'status': 'success',
            'message': 'Event updated successfully',
            'data': event.to_dict()
        })
    except Exception as e:
        db.session.rollback()
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

@event_bp.route('/events/<int:id>', methods=['DELETE'])
def destroy(id):
    try:
        event = Event.query.get(id)
        if not event:
            return jsonify({
                'status': 'error',
                'message': 'Event not found'
            }), 404
            
        db.session.delete(event)
        db.session.commit()
        
        return jsonify({
            'status': 'success',
            'message': 'Event deleted successfully'
        })
    except Exception as e:
        db.session.rollback()
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 500

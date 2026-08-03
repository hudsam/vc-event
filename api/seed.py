import sys
from app import create_app
from models import db, User, Event
from datetime import datetime
import bcrypt

def seed_database(reset=False):
    app = create_app()
    with app.app_context():
        if reset:
            print("Resetting database tables...")
            db.drop_all()
            
        print("Creating database tables if they do not exist...")
        db.create_all()
        
        # 1. Seed Users
        users_data = [
            {
                'name': 'huda',
                'email': 'huda@maxy.academy',
                'password': 'password'
            },
            {
                'name': 'smith',
                'email': 'smith@maxy.academy',
                'password': 'password'
            }
        ]
        
        print("Seeding users...")
        for u in users_data:
            existing = User.query.filter_by(email=u['email']).first()
            if not existing:
                # Hash the password with bcrypt (rounds=12 to match BCRYPT_ROUNDS in Laravel)
                hashed_pw = bcrypt.hashpw(u['password'].encode('utf-8'), bcrypt.gensalt(rounds=12)).decode('utf-8')
                new_user = User(
                    name=u['name'],
                    email=u['email'],
                    password=hashed_pw
                )
                db.session.add(new_user)
                print(f"Created user: {u['name']} ({u['email']})")
            else:
                print(f"User {u['email']} already exists.")
                
        # 2. Seed Events
        events_data = [
            {
                'title': 'Tech Conference 2026',
                'slug': 'tech-conference-2026',
                'category': 'Technology',
                'venue': 'Jakarta Convention Center',
                'organizer': 'Maxy Academy',
                'description': 'Konferensi teknologi terbesar tahun ini, membahas AI, Cloud, dan Cybersecurity.',
                'thumbnail': 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80',
                'banner': 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1200&q=80',
                'start_date': datetime(2026, 9, 15, 9, 0, 0),
                'end_date': datetime(2026, 9, 15, 17, 0, 0),
                'status': 'published'
            },
            {
                'title': 'Digital Marketing Workshop',
                'slug': 'digital-marketing-workshop',
                'category': 'Marketing',
                'venue': 'Online (Zoom)',
                'organizer': 'Maxy Academy',
                'description': 'Pelatihan intensif tentang strategi digital marketing untuk bisnis skala kecil dan menengah.',
                'thumbnail': 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&q=80',
                'banner': 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=1200&q=80',
                'start_date': datetime(2026, 8, 20, 13, 0, 0),
                'end_date': datetime(2026, 8, 20, 16, 0, 0),
                'status': 'published'
            },
            {
                'title': 'Startup Founders Meetup',
                'slug': 'startup-founders-meetup',
                'category': 'Business',
                'venue': 'Co-working Space Sudirman',
                'organizer': 'Maxy Academy',
                'description': 'Ajang kumpul dan networking para pendiri startup untuk berbagi pengalaman dan mencari investor.',
                'thumbnail': 'https://images.unsplash.com/photo-1515169067868-5387ec356754?w=600&q=80',
                'banner': 'https://images.unsplash.com/photo-1515169067868-5387ec356754?w=1200&q=80',
                'start_date': datetime(2026, 10, 5, 18, 30, 0),
                'end_date': datetime(2026, 10, 5, 21, 0, 0),
                'status': 'published'
            }
        ]
        
        print("Seeding events...")
        for ev in events_data:
            existing = Event.query.filter_by(slug=ev['slug']).first()
            if not existing:
                new_event = Event(**ev)
                db.session.add(new_event)
                print(f"Created event: {ev['title']}")
            else:
                print(f"Event {ev['title']} already exists.")
                
        db.session.commit()
        print("Database seeding completed!")

if __name__ == '__main__':
    reset_db = '--reset' in sys.argv
    seed_database(reset=reset_db)

from flask import Flask, jsonify
from flask_cors import CORS
from models import db
from config import Config
from routes import auth_bp, event_bp

app = Flask(__name__)

def create_app(config_class=Config):
    app.config.from_object(config_class)
    
    # Configure CORS
    CORS(app)
    
    # Initialize DB
    db.init_app(app)
    
    # Register blueprints
    app.register_blueprint(auth_bp, url_prefix='/api')
    app.register_blueprint(event_bp, url_prefix='/api')
    
    @app.route('/')
    def welcome():
        return jsonify({
            'message': 'Welcome to Maxy Academy Platform Event API (Python/Flask)',
            'status': 'success'
        })
        
    return app

if __name__ == '__main__':
    app = create_app()
    app.run(host='0.0.0.0', port=8000, debug=app.config['DEBUG'])

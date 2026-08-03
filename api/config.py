import os
from dotenv import load_dotenv

# Load env file
load_dotenv()

class Config:
    SECRET_KEY = os.environ.get('APP_KEY')
    DEBUG = os.environ.get('APP_DEBUG', 'true').lower() == 'true'
    
    # DB configuration
    DB_CONN = os.environ.get('DB_CONNECTION', 'mysql')
    DB_HOST = os.environ.get('DB_HOST', 'localhost')
    DB_PORT = os.environ.get('DB_PORT', '3306')
    DB_DATABASE = os.environ.get('DB_DATABASE', 'database')
    DB_USERNAME = os.environ.get('DB_USERNAME', 'username')
    DB_PASSWORD = os.environ.get('DB_PASSWORD', 'password')

    if DB_CONN == 'sqlite':
        SQLALCHEMY_DATABASE_URI = f"sqlite:///{os.path.join(os.path.abspath(os.path.dirname(__file__)), DB_DATABASE + '.sqlite')}"
    else:
        # mysql using pymysql driver
        SQLALCHEMY_DATABASE_URI = f"mysql+pymysql://{DB_USERNAME}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_DATABASE}"
        
    SQLALCHEMY_TRACK_MODIFICATIONS = False

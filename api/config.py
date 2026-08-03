import os
from dotenv import load_dotenv

# Load env file
load_dotenv()

class Config:
    SECRET_KEY = os.environ.get('APP_KEY')
    DEBUG = os.environ.get('APP_DEBUG', 'true').lower() == 'true'
    
    # DB configuration
    DB_CONN = os.environ.get('DB_CONNECTION')
    DB_HOST = os.environ.get('DB_HOST')
    DB_PORT = os.environ.get('DB_PORT')
    DB_DATABASE = os.environ.get('DB_DATABASE')
    DB_USERNAME = os.environ.get('DB_USERNAME')
    DB_PASSWORD = os.environ.get('DB_PASSWORD')

    if DB_CONN == 'mysql':
        SQLALCHEMY_DATABASE_URI = f"mysql+pymysql://{DB_USERNAME}:{DB_PASSWORD}@{DB_HOST}:{DB_PORT}/{DB_DATABASE}"
    
    SQLALCHEMY_TRACK_MODIFICATIONS = False

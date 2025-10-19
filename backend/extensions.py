from flask_sqlalchemy import SQLAlchemy
import os
from flask import session


db = SQLAlchemy()

DEBUG = os.environ.get("DEBUG", "false") == "true"

def debug_print(message):
    if DEBUG:
        print(f"[DEBUG] {message}")

def get_current_actor():
    from models import User
    user_id = session.get("user_id")
    if not user_id:
        return None
    return User.query.get(user_id)

def get_system_actor():
    from models import User
    system_user = User.query.filter_by(username="System").first()
    return system_user
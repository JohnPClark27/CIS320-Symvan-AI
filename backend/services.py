from models import db, User, Event, Organization, Event, Member

def create_user(username, password_hash, email=None):
    user = User(username = username, password_hash = password_hash, email = email)
    db.session.add(user)
    db.session.commit()
    return user

def create_organization(name, description=None):
    org = Organization(name = name, description = description)
    db.session.add(org)
    db.session.commit()
    return org
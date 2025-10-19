# backend/services.py
# Provides service function to manage database operations
# Version: 1.0
# Date: 2025-10-15
from extensions import db
from models import  User, Event, Organization, Member
import bcrypt
from extensions import *
 
# Permission check
def has_permission(actor, required_level):
    if not actor:
        return False
    map = {"User": 1, "Moderator": 3, "Admin": 5}
    return map.get(actor.level, 0) >= required_level

# Create functions
def create_user(actor, username,level, password, email=None):
    if has_permission(actor, 5) == False:
        return "Permission denied"
    
    existing_user = get_user_by_username(username)
    if existing_user:
        return "Username already exists"
    
    password_hash = bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt())
    
    user = User(username = username, level = level, password_hash = password_hash, email = email, modified_by = actor.id)
    db.session.add(user)
    db.session.flush()

    return user

def create_organization(actor, name, description=None):
    if has_permission(actor, 3) == False:
        return "Permission denied"
    
    existing_org = get_organization_by_name(name)
    if existing_org:
        return "Organization name already exists"
    
    org = Organization(name = name, description = description, modified_by = actor.id)
    db.session.add(org)
    db.session.flush()
    return org

def create_event(actor, name, organization_id, details=None, date=None, start_time=None, end_time=None, location=None):
    permissions = get_member_permissions(actor.id, organization_id)

    if permissions == "Member":
        return "Invalid Permissions"

    event = get_event_by_name(name)
    if not event:
        event = Event(name = name, organization_id = organization_id, details = details, date = date, start_time = start_time, end_time = end_time, location = location, status = "Draft", modified_by = actor.id)
        db.session.add(event)
        db.session.flush()
        return event
    return "Event name already exists"

def add_member(actor, user_id, organization_id, permission_level="Member"):
    if has_permission(actor, 3) == False:
        return "Permission denied"
    
    member = Member.query.filter_by(user_id=user_id, organization_id=organization_id).first()
    if not member:
        member = Member(user_id = user_id, organization_id = organization_id, permission_level = permission_level, modified_by = actor.id)
        db.session.add(member)
        db.session.flush()
        return member
    return "User is already a member of this organization"

def remove_member(user_id, organization_id):
    member = Member.query.filter_by(user_id=user_id, organization_id=organization_id).first()
    if not member:
        return "Member not found"
    db.session.delete(member)
    db.session.flush()
    return "Member removed"

# Read functions
def get_user(user_id):
    return User.query.get(user_id)

def get_user_by_username(username):
    return User.query.filter_by(username=username).first()

def get_organization(org_id):
    return Organization.query.get(org_id)

def get_organization_by_name(name):
    return Organization.query.filter_by(name = name).first()

def get_event(event_id):
    return Event.query.get(event_id)

def get_event_by_name(name):
    return Event.query.filter_by(name=name).first()

def get_events_by_org(org_id):
    return Event.query.filter_by(organization_id=org_id).all()

def get_members_by_org(org_id):
    return Organization.query.get(org_id).members

def get_orgs_by_user(user_id):
    user = User.query.get(user_id)
    if user:
        return [member.organization for member in user.memberships]
    return []

def get_posted_events():
    return Event.query.filter_by(status="Posted").all()

def get_org_posted_events(org_id):
    return Event.query.filter_by(organization_id=org_id, status="Posted").all()

def get_org_draft_events(org_id):
    return Event.query.filter_by(organization_id=org_id, status="Draft").all()

def get_member_permissions(user_id, org_id):
    member = Member.query.filter_by(user_id=user_id, organization_id=org_id).first()
    if member:
        return member.permission_level
    return None

def get_all_users():
    return User.query.all()

def get_all_organizations():
    return Organization.query.all()

def get_all_events():
    return Event.query.all()

def get_user_level(user_id):
    user = get_user(user_id)
    if user:
        if user.level == "Admin":
            return 5
        elif user.level == "Moderator":
            return 3
        elif user.level == "User":
            return 1

# Update functions
def update(obj, **kwargs):
    
    if not obj:
        return None
    for key, value in kwargs.items():
        if hasattr(obj , key):
            setattr(obj,key, value)
    db.session.flush()
    return obj.id

# This is for when we are ready to post an event.
def post_event(event_id):
    event = get_event(event_id)
    if not event:
        return "Event not found"
    
    columns = event.__table__.columns.keys()

    missing_fields = []

    for col in columns:
        if getattr(event, col) is None:
            missing_fields.append(col)
    if missing_fields:
        return f"Missing fields: {', '.join(missing_fields)}"
        
    event.status = "Posted"
    db.session.flush()
    return "Event posted successfully"

# Remove functions
def remove_user(user_id):
    user = get_user(user_id)
    if not user:
        return "User not found"
    username = user.username
    db.session.delete(user)
    db.session.flush()
    return f"User {username} removed"

def remove_organization(org_id):
    org = get_organization(org_id)
    if not org:
        return "Organization not found"
    name = org.name
    db.session.delete(org)
    db.session.flush()
    return f"Organization {name} removed"

def remove_event(event_id):
    event = get_event(event_id)
    if not event:
        return "Event not found"
    name = event.name
    db.session.delete(event)
    db.session.flush()
    return f"Event {name} removed"

# User Functions
def verify_password(username, password):
    debug_print(f"Attempting to verify password for: {username}")
    user = get_user_by_username(username)
    if not user:
        return False
    debug_print(f"Verifying password for user: {username}")
    return bcrypt.checkpw(password.encode("utf-8"), user.password_hash)

def change_password(actor_id, username, new_password):
    debug_print(f"Changing password for: {username}")
    user = get_user_by_username(username)
    if not user:
        debug_print(f"User {username} not found")
        return False
    user.password_hash = bcrypt.hashpw(new_password.encode("utf-8"), bcrypt.gensalt())
    db.session.flush()
    debug_print(f"Password changed for: {username}")
    return True
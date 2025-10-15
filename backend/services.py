# backend/services.py
# Provides service function to manage database operations
# Version: 1.0
# Date: 2025-10-15

from models import db, User, Event, Organization, Event, Member

# Create functions
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

def create_event(name, organization_id, details=None, date=None, start_time=None, end_time=None, location=None):
    event = Event(name = name, organization_id = organization_id, details = details, date = date, start_time = start_time, end_time = end_time, location = location, status = "Draft")
    db.session.add(event)
    db.session.commit()
    return event

def add_member(user_id, organization_id, permission_level="Member"):
    member = Member(user_id = user_id, organization_id = organization_id, permission_level = permission_level)
    db.session.add(member)
    db.session.commit()
    return member

def remove_member(user_id, organization_id):
    member = Member.query.filter_by(user_id=user_id, organization_id=organization_id).first()
    if not member:
        return "Member not found"
    db.session.delete(member)
    db.session.commit()
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

def get_user_permission(user_id, org_id):
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

# Update functions
def update(obj, **kwargs):
    if not obj:
        return None
    for key, value in kwargs.items():
        if hasattr(obj , key):
            setattr(obj,key, value)
    db.session.commit()
    return obj

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
    db.session.commit()
    return "Event posted successfully"

# Remove functions
def remove_user(user_id):
    user = get_user(user_id)
    if not user:
        return "User not found"
    username = user.username
    db.session.delete(user)
    db.session.commit()
    return f"User {username} removed"

def remove_organization(org_id):
    org = get_organization(org_id)
    if not org:
        return "Organization not found"
    name = org.name
    db.session.delete(org)
    db.session.commit()
    return f"Organization {name} removed"

def remove_event(event_id):
    event = get_event(event_id)
    if not event:
        return "Event not found"
    name = event.name
    db.session.delete(event)
    db.session.commit()
    return f"Event {name} removed"
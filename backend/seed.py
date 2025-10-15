from datetime import datetime, time
from models import db, User, Event, Organization, Member




def seed_users():
    return [
        User(username="JohnDoe", password_hash="NA")
        # Add more default users if needed
    ]

def seed_organizations():
    return [
        Organization(
            name="Computer Science Club",
            description="A club for students to build connections and learn more about special topics in computer science."
        )
    ]

def seed_events(org_id):
    return [
        Event(
            name = "Coding Meeting",
            organization_id = org_id,
            details = "Weekly coding meeting",
            date = datetime(2024, 11, 1),
            start_time = time(18,0),
            end_time = time(19,0),
            location = "Ott Rm 121"
        )
    ]

def create_membership(user_id, org_id):
    return [
        Member(user_id=user_id, organization_id=org_id)
    ]

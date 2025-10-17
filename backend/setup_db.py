from app import app, db
from models import User, Organization, Event, Member
from services import *
from datetime import date, time

with app.app_context():
    print("Dropping existing tables...")
    db.drop_all()

    print("Creating tables...")
    db.create_all()

    print("Seeding initial data...")
    
    user = create_user("JohnDoe","User", "NA")
    org = create_organization("Computer Science Club", "A club for students to build connections and learn more about special concepts in computer science.")

    event = create_event("Coding Meeting", org.id, location="Ott Rm 121")
    add_member(user.id, org.id, permission_level="Admin")

    user2 = create_user("JaneSmith","User", "NA")
    add_member(user2.id, org.id, permission_level="Member")

    db.session.commit()
    print("Database Seeded")
    
    print(post_event(event.id))

    update(event, details = "Weekly coding meeting.", date = date(2025,11,1), start_time = time(18,0), end_time = time(19,0))

    print(post_event(event.id))
    
    db.session.commit()

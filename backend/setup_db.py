from app import app

from services import *
from datetime import date, time
from extensions import *
import sys
import os

def setup():
    
    with app.app_context():
        inspector = db.inspect(db.engine)
        tables = inspector.get_table_names()

        if not tables:
            db.drop_all()

            db.create_all()
            print("Database tables created.")

        print("Seeding initial data...")

        admin_username = os.environ.get("ADMIN_USERNAME")
        admin_password = os.environ.get("ADMIN_PASSWORD")
        system_password = os.environ.get("SYSTEM_PASSWORD")

        if not admin_username or not admin_password or not system_password:
            print("Admin credentials missing in environment variables.")
            return
    
        existing_admin = User.query.filter_by(username = admin_username).first()
        existing_system = User.query.filter_by(username = "System").first()
        
        if existing_system:
            print("System user already exist.")
        else:
            system = User(level = "Admin", username = "System", password_hash = bcrypt.hashpw(system_password.encode("utf-8"), bcrypt.gensalt()))
            db.session.add(system)
            db.session.flush()

        if existing_admin:
            print("Admin user already exists.")
        else:
            admin = User(level = "Admin", username = admin_username, password_hash = bcrypt.hashpw(admin_password.encode("utf-8"), bcrypt.gensalt()))
            db.session.add(admin)
        
         
        db.session.commit()
        print("Initial users created.")
        app._admin_checked = True
    
    
        system = get_system_actor()

        user = create_user(system, "JohnDoe","User", "password")
        if isinstance(user, str):
            print(f"Error creating user: {user}")
            user = get_user_by_username("JohnDoe")
            if not user:
                print("Failed to retrieve existing user JohnDoe.")
                return
        org = create_organization(system, "Computer Science Club", "A club for students to build connections and learn more about special concepts in computer science.")

        if isinstance(org, str):
            print(f"Error creating organization: {org}")
            org = get_organization_by_name("Computer Science Club")
            if not org:
                print("Failed to retrieve existing organization Computer Science Club.")
                return
        db.session.flush()
        add_member(user, user.id, org.id, permission_level="Owner")

        user2 = create_user(system,"JaneSmith","User", "password")
        if isinstance(user2, str):
            print(f"Error creating user: {user2}")
            user2 = get_user_by_username("JaneSmith")
            if not user2:
                print("Failed to retrieve existing user JaneSmith.")
                return
        add_member(user2, user2.id, org.id, permission_level="Member")

        event = create_event(user, "Coding Meeting", org.id, location="Ott Rm 121")
        if isinstance(event, str):
            print(f"Error creating event: {event}")
            event = get_event_by_name("Coding Meeting")
            if not event:
                print("Failed to retrieve existing event Coding Meeting.")
                return
        
        db.session.commit()
        print("Database Seeded")
    
        print(post_event(user,event.id))

        print(update_event(user, event.id, details = "Weekly coding meeting.", date = date(2025,11,1), start_time = time(18,0), end_time = time(19,0)))

        print(post_event(user,event.id))

        db.session.commit()

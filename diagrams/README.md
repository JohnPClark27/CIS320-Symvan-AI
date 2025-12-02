# Symvan AI - Project Diagrams Documentation

This directory contains comprehensive UML and architectural diagrams for the Symvan AI Event Management System. These diagrams provide a complete visual representation of the system's architecture, database design, workflows, and components.

## 📋 Table of Contents

1. [Overview](#overview)
2. [Diagram List](#diagram-list)
3. [How to View Diagrams](#how-to-view-diagrams)
4. [Diagram Descriptions](#diagram-descriptions)
5. [Technology Stack](#technology-stack)

---

## Overview

Symvan AI is a full-stack event management system designed for Indiana Wesleyan University students and faculty. The system allows users to:
- Browse and enroll in campus events
- Create and manage organizations
- Plan events with task management
- Receive notifications
- Interact with an AI-powered chatbot

These diagrams provide detailed documentation for understanding the system architecture, database relationships, user workflows, and component interactions.

---

## Diagram List

| # | Diagram Name | File | Type | Description |
|---|--------------|------|------|-------------|
| 1 | Entity Relationship Diagram | `01_entity_relationship_diagram.puml` | ERD | Database entities and relationships |
| 2 | Class Diagram | `02_class_diagram.puml` | UML | Object-oriented class structure |
| 3 | System Architecture | `03_system_architecture.puml` | Architecture | Full system architecture layers |
| 4 | Use Case Diagram | `04_use_case_diagram.puml` | UML | User interactions and system features |
| 5 | Component Diagram | `05_component_diagram.puml` | UML | System components and dependencies |
| 6 | Login Sequence | `06_sequence_user_login.puml` | Sequence | User authentication flow |
| 7 | Event Creation Sequence | `07_sequence_event_creation.puml` | Sequence | Event creation workflow |
| 8 | Event Enrollment Sequence | `08_sequence_event_enrollment.puml` | Sequence | User enrollment process |
| 9 | Task Management Sequence | `09_sequence_task_management.puml` | Sequence | Task planning workflow |
| 10 | Organization Membership Sequence | `10_sequence_organization_membership.puml` | Sequence | Organization joining process |
| 11 | Detailed Database Schema | `11_database_schema_detailed.puml` | Schema | Complete database specification |
| 12 | Data Flow Diagram | `12_data_flow_diagram.puml` | DFD | Data flow through system |
| 13 | State Diagrams | `13_state_diagrams.puml` | State | Entity lifecycle states |
| 14 | Deployment Diagram | `14_deployment_diagram.puml` | Deployment | Infrastructure and deployment |
| 15 | Activity Diagram | `15_activity_diagram.puml` | Activity | Event management workflow |

---

## How to View Diagrams

These diagrams are created using **PlantUML**, a text-based diagramming tool. You have several options to view them:

### Option 1: Online PlantUML Editor (Recommended)
1. Go to [PlantUML Online Editor](http://www.plantuml.com/plantuml/uml/)
2. Copy and paste the content of any `.puml` file
3. The diagram will render automatically

### Option 2: VS Code Extension
1. Install the "PlantUML" extension in VS Code
2. Open any `.puml` file
3. Press `Alt+D` to preview the diagram

### Option 3: Command Line
```bash
# Install PlantUML (requires Java)
brew install plantuml  # macOS
apt-get install plantuml  # Linux
# Or download from: https://plantuml.com/download

# Generate PNG images
plantuml diagrams/*.puml

# Generate SVG images
plantuml -tsvg diagrams/*.puml
```

### Option 4: IDE Plugins
- **IntelliJ IDEA**: PlantUML integration plugin
- **Eclipse**: PlantUML plugin
- **Atom**: plantuml-viewer package

---

## Diagram Descriptions

### 1. Entity Relationship Diagram (ERD)
**File:** `01_entity_relationship_diagram.puml`

Shows the database structure with 8 main entities:
- **user**: System users (students/faculty)
- **user_profile**: Extended user information and preferences
- **organization**: Campus organizations/clubs
- **member**: User-organization relationships with permissions
- **event**: Campus events with details and status
- **enrollment**: User event registrations
- **task**: To-do items for event planning
- **audit_log**: Action logging for compliance

**Key Relationships:**
- Users can belong to multiple organizations
- Organizations create multiple events
- Users enroll in multiple events
- Tasks can be linked to events or organizations
- All actions are logged in audit_log

---

### 2. UML Class Diagram
**File:** `02_class_diagram.puml`

Object-oriented representation of the system with classes, attributes, methods, and relationships. Each class corresponds to a database table and includes:
- Private attributes (database columns)
- Public methods (operations)
- Relationships and multiplicities
- Database connection layer

**Key Classes:**
- `User`: Authentication and user management
- `Event`: Event lifecycle management
- `Organization`: Organization operations
- `Task`: Task planning with status tracking
- `AuditLog`: Immutable action logging

---

### 3. System Architecture Diagram
**File:** `03_system_architecture.puml`

Three-tier architecture showing:

**Presentation Layer:**
- Web Browser
- HTML/CSS/JavaScript

**Application Layer (PHP):**
- Frontend PHP pages (index, calendar, events, etc.)
- API endpoints (enrollment, updates, deletions)
- Core components (database, session, authentication)

**Data Layer:**
- MySQL Database with 8 tables
- Data relationships and constraints

**External Services:**
- OpenAI API (chatbot)
- Email service (notifications)
- SMS service (alerts)

---

### 4. Use Case Diagram
**File:** `04_use_case_diagram.puml`

Defines system functionality from user perspective:

**Actors:**
- Student (base user)
- Faculty (extends student)
- Organization Admin (extends student with management rights)
- System Admin (full access)
- AI Chatbot (automated assistant)

**Major Use Cases:**
- Authentication & authorization
- Event discovery & enrollment
- Event management (create, edit, delete)
- Organization management
- Task planning
- Profile management
- AI assistance
- Audit logging

---

### 5. Component Diagram
**File:** `05_component_diagram.puml`

Shows system components and their dependencies:

**Frontend Components:**
- Page controllers (PHP files)
- API controllers (endpoints)
- Shared components (navbar, footer, db_connect)

**Business Logic:**
- User Service
- Event Service
- Organization Service
- Enrollment Service
- Task Service
- Audit Service
- Notification Service

**Data Access:**
- Repositories for each entity
- Direct MySQL connection

---

### 6-10. Sequence Diagrams
Detailed interaction flows for key workflows:

#### 6. User Login (`06_sequence_user_login.puml`)
- Credential validation
- Session creation
- Audit logging
- Dashboard redirect

#### 7. Event Creation (`07_sequence_event_creation.puml`)
- Organization membership check
- Draft event creation
- Validation and uniqueness
- Audit trail

#### 8. Event Enrollment (`08_sequence_event_enrollment.puml`)
- Event browsing
- Enrollment validation
- Attendee count update
- Success confirmation

#### 9. Task Management (`09_sequence_task_management.puml`)
- Task creation
- Status updates (To Do → In Progress → Completed)
- Task deletion
- Ownership verification

#### 10. Organization Membership (`10_sequence_organization_membership.puml`)
- Organization browsing
- Password-protected joining
- Permission levels (Member/Admin)
- Member management

---

### 11. Detailed Database Schema
**File:** `11_database_schema_detailed.puml`

Complete database specification including:
- Table structures with data types
- Primary keys and auto-increment
- Foreign keys with cascade rules
- Unique constraints
- Indexes for performance
- Default values
- Enum types for status fields

**Key Features:**
- Referential integrity enforcement
- Cascade deletes for cleanup
- Unique constraints prevent duplicates
- Indexes for query optimization

---

### 12. Data Flow Diagram
**File:** `12_data_flow_diagram.puml`

Shows how data moves through the system:

**Level 0:** Context diagram showing external entities
**Level 1:** Detailed processes including:
1. User Management
2. Event Management
3. Organization Management
4. Task Planning
5. Notification Service
6. Audit & Logging
7. AI Assistant

**Data Stores:**
- D1: User Data
- D2: Event Data
- D3: Organization Data
- D4: Enrollment Data
- D5: Task Data
- D6: Audit Log
- D7: Profile Data

---

### 13. State Diagrams
**File:** `13_state_diagrams.puml`

Lifecycle states for key entities:

**Event Lifecycle:**
- Draft → Posted → Completed/Deleted
- State transitions trigger notifications

**Task Lifecycle:**
- To Do → In Progress → Completed
- Can be reopened or deleted

**User Session:**
- Logged Out → Logged In (Active/Idle) → Logged Out
- Session timeout and security events

**Enrollment Lifecycle:**
- Not Enrolled → Enrolled → Cancelled
- Attendee count tracking

---

## Technology Stack

### Frontend
- **HTML/CSS**: User interface
- **JavaScript**: Client-side interactivity
- **PHP**: Server-side rendering

### Backend
- **PHP 8.3+**: Application logic
- **MySQLi**: Database connectivity
- **Session Management**: PHP sessions

### Database
- **MySQL 8.0+**: Relational database
- **InnoDB**: Storage engine with ACID compliance
- **utf8mb4**: Character set for full Unicode support

### External Services
- **OpenAI API**: AI-powered chatbot
- **SMTP**: Email notifications
- **SMS Gateway**: Text message alerts

### Development Tools
- **XAMPP**: Local development environment
- **phpMyAdmin**: Database management
- **Git**: Version control

---

## Database Key Constraints

### Referential Integrity
All foreign keys enforce referential integrity with appropriate cascade rules:
- `ON DELETE CASCADE`: Automatically delete related records
- `ON DELETE SET NULL`: Nullify foreign key on parent delete

### Unique Constraints
- User: username, email
- Organization: name
- Event: name
- Enrollment: (user_id, event_id) composite
- Member: (user_id, organization_id) composite
- UserProfile: user_id

### Indexes
Performance indexes on:
- Foreign keys for join optimization
- Frequently queried columns
- Composite keys for relationship tables

---

## Security Features

### Authentication
- Bcrypt password hashing
- Session-based authentication
- Password change forces logout

### Authorization
- Role-based access control (User level)
- Organization permission levels (Member/Admin)
- Owner-based task editing

### Audit Trail
- All critical actions logged
- Immutable audit records
- Timestamp tracking
- User and affected entity tracking

---

## Event Status Workflow

```
Draft (Organization members only)
  ↓
  ↓ [Admin promotes]
  ↓
Posted (Visible to all, enrollments allowed)
  ↓
  ↓ [Date passes OR admin deletes]
  ↓
Completed/Deleted
```

---

## Task Status Workflow

```
To Do (Initial state)
  ↓
  ↓ [User starts work]
  ↓
In Progress (Work ongoing)
  ↓
  ↓ [User completes]
  ↓
Completed (Can be reopened)
```

---

## Notes for Presentation

When presenting this project, consider highlighting:

1. **Full-Stack Architecture**: PHP backend, MySQL database, responsive frontend
2. **Security**: Password hashing, audit logging, access control
3. **Scalability**: Indexed database, efficient queries, modular design
4. **User Experience**: AI chatbot, notifications, calendar view
5. **Data Integrity**: Foreign keys, unique constraints, validation
6. **Workflow Management**: Draft→Posted events, task status tracking
7. **Audit Trail**: Complete action logging for compliance
8. **Multi-tenancy**: Organization-based event management

---

## Future Enhancement Opportunities

Based on the current architecture, potential enhancements could include:
- REST API for mobile app integration
- Real-time notifications via WebSockets
- Advanced search with Elasticsearch
- Analytics dashboard for organizations
- Event recommendation algorithm (ML-based)
- Calendar integration (Google Calendar, Outlook)
- Social features (comments, ratings, sharing)
- Media uploads for events (images, documents)

---

## Credits

**Project:** CIS320-Symvan-AI  
**Institution:** Indiana Wesleyan University  
**Purpose:** Campus event management system  
**Technology:** PHP, MySQL, HTML/CSS, OpenAI  

---

## License

This project is for educational purposes at Indiana Wesleyan University.

---

## Contact

For questions about these diagrams or the project architecture, please refer to the main project repository or contact the development team.

---

### 14. Deployment Diagram
**File:** `14_deployment_diagram.puml`

Infrastructure and deployment architecture:

**Client Tier:**
- Web browsers (desktop/mobile)
- Session storage
- JavaScript execution

**Web Server Tier:**
- Apache HTTP Server with SSL/TLS
- PHP 8.3 runtime with FPM
- Application files and components
- Composer dependencies

**Database Tier:**
- MySQL 8.0 with InnoDB engine
- Query optimizer and index manager
- Transaction management
- Security and access control

**External Services:**
- OpenAI API platform
- SMTP email service
- SMS gateway

**File System:**
- Configuration files (.env, php.ini, my.cnf)
- Log files (Apache, PHP, MySQL, application)

**Development Tools:**
- Git version control
- phpMyAdmin database management

**Deployment Options:**
- XAMPP (local development)
- LAMP stack (production)
- Cloud hosting (AWS/Azure/GCP)

---

### 15. Activity Diagram
**File:** `15_activity_diagram.puml`

Complete event management workflow with swimlanes:

**Student/Faculty Activities:**
- Login and authentication
- Browse events and calendar
- Enroll in events
- View profile and preferences
- Receive notifications

**Organization Admin Activities:**
- Create events (Draft → Posted)
- Manage organization members
- Create and track tasks
- Update event details
- View statistics

**System Activities:**
- Authentication and authorization
- Data validation
- Database operations
- Audit logging
- Notifications
- Business rule enforcement

**Key Workflows:**
- Complete event lifecycle from creation to completion
- Task management process
- Enrollment validation and tracking
- Parallel activity handling (fork/join)
- Decision points and conditional flows

---

**Last Updated:** December 2025  
**Diagram Format:** PlantUML (.puml)  
**Total Diagrams:** 15

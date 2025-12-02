# Symvan AI - Diagrams Index

## 📂 Complete Diagram Collection

This directory contains **15 comprehensive diagrams** documenting the Symvan AI Event Management System, plus 3 documentation guides.

---

## 🚀 Quick Start

**New to these diagrams?** Start here:
1. Read [QUICK_REFERENCE.md](QUICK_REFERENCE.md) - Find the right diagram fast
2. View [03_system_architecture.puml](03_system_architecture.puml) - Understand the big picture
3. Check [README.md](README.md) - Complete documentation

**Preparing a presentation?** 
→ See [PRESENTATION_GUIDE.md](PRESENTATION_GUIDE.md)

---

## 📊 All Diagrams

### Database & Data Model (3 diagrams)
| # | Name | File | Size | Complexity |
|---|------|------|------|------------|
| 01 | Entity-Relationship Diagram | [01_entity_relationship_diagram.puml](01_entity_relationship_diagram.puml) | 2.9KB | 🟢 Beginner |
| 11 | Detailed Database Schema | [11_database_schema_detailed.puml](11_database_schema_detailed.puml) | 6.0KB | 🔴 Advanced |
| 12 | Data Flow Diagram | [12_data_flow_diagram.puml](12_data_flow_diagram.puml) | 5.6KB | 🟡 Intermediate |

### Architecture & Design (3 diagrams)
| # | Name | File | Size | Complexity |
|---|------|------|------|------------|
| 02 | UML Class Diagram | [02_class_diagram.puml](02_class_diagram.puml) | 4.6KB | 🔴 Advanced |
| 03 | System Architecture | [03_system_architecture.puml](03_system_architecture.puml) | 4.5KB | 🟢 Beginner |
| 05 | Component Diagram | [05_component_diagram.puml](05_component_diagram.puml) | 5.7KB | 🟡 Intermediate |

### User Interaction & Features (2 diagrams)
| # | Name | File | Size | Complexity |
|---|------|------|------|------------|
| 04 | Use Case Diagram | [04_use_case_diagram.puml](04_use_case_diagram.puml) | 4.5KB | 🟢 Beginner |
| 15 | Activity Diagram | [15_activity_diagram.puml](15_activity_diagram.puml) | 4.4KB | 🟡 Intermediate |

### Workflows & Sequences (5 diagrams)
| # | Name | File | Size | Complexity |
|---|------|------|------|------------|
| 06 | User Login Sequence | [06_sequence_user_login.puml](06_sequence_user_login.puml) | 1.9KB | 🔴 Advanced |
| 07 | Event Creation Sequence | [07_sequence_event_creation.puml](07_sequence_event_creation.puml) | 2.6KB | 🔴 Advanced |
| 08 | Event Enrollment Sequence | [08_sequence_event_enrollment.puml](08_sequence_event_enrollment.puml) | 2.8KB | 🔴 Advanced |
| 09 | Task Management Sequence | [09_sequence_task_management.puml](09_sequence_task_management.puml) | 3.6KB | 🔴 Advanced |
| 10 | Organization Membership Sequence | [10_sequence_organization_membership.puml](10_sequence_organization_membership.puml) | 4.9KB | 🔴 Advanced |

### System Behavior (2 diagrams)
| # | Name | File | Size | Complexity |
|---|------|------|------|------------|
| 13 | State Diagrams | [13_state_diagrams.puml](13_state_diagrams.puml) | 6.6KB | 🟡 Intermediate |
| 14 | Deployment Diagram | [14_deployment_diagram.puml](14_deployment_diagram.puml) | 6.9KB | 🟡 Intermediate |

---

## 📚 Documentation Guides

| Guide | File | Size | Purpose |
|-------|------|------|---------|
| **Main Documentation** | [README.md](README.md) | 15KB | Complete guide to all diagrams |
| **Presentation Guide** | [PRESENTATION_GUIDE.md](PRESENTATION_GUIDE.md) | 17KB | 90-minute presentation structure |
| **Quick Reference** | [QUICK_REFERENCE.md](QUICK_REFERENCE.md) | 8.5KB | Fast diagram lookup |
| **This Index** | [INDEX.md](INDEX.md) | - | You are here |

---

## 🎯 Recommended Learning Path

### Path 1: For Students/Newcomers
```
Start → 03 System Architecture → 04 Use Case → 01 ERD → 15 Activity → Done
Time: ~30 minutes
```

### Path 2: For Developers
```
Start → 02 Class Diagram → 11 Database Schema → 05 Component → Sequences (06-10) → Done
Time: ~1 hour
```

### Path 3: For Architects
```
Start → 03 Architecture → 05 Component → 14 Deployment → 12 Data Flow → All Sequences → Done
Time: ~1.5 hours
```

### Path 4: For Presenters
```
Start → PRESENTATION_GUIDE.md → 03 Architecture → 04 Use Case → 01 ERD → Pick 2 Sequences → Done
Time: ~45 minutes
```

---

## 🔍 Find a Diagram By...

### By What You Need to Understand
- **"How is data organized?"** → 01, 11
- **"What can users do?"** → 04, 15
- **"How does it work?"** → 06, 07, 08, 09, 10
- **"How is it structured?"** → 02, 03, 05
- **"How is it deployed?"** → 14
- **"How does data move?"** → 12
- **"What are the states?"** → 13

### By Your Role
- **Database Admin** → 01, 11, 12
- **Backend Developer** → 02, 05, 06-10, 11
- **Frontend Developer** → 04, 06-10, 03
- **DevOps Engineer** → 14, 03, 05
- **Project Manager** → 04, 03, 13
- **Security Analyst** → 11, 06-10, 03, 12

### By Diagram Type
- **UML Diagrams** → 02, 04, 05, 06-10, 13, 15
- **Data Diagrams** → 01, 11, 12
- **Architecture Diagrams** → 03, 05, 14
- **Behavioral Diagrams** → 06-10, 13, 15

---

## 📖 How to View Diagrams

### Online (No Installation)
```
1. Go to: http://www.plantuml.com/plantuml/uml/
2. Open any .puml file from this directory
3. Copy the entire content
4. Paste into the online editor
5. View the rendered diagram
```

### VS Code (Recommended)
```
1. Install "PlantUML" extension
2. Open any .puml file
3. Press Alt+D (Windows/Linux) or Option+D (Mac)
4. Diagram appears in preview pane
```

### Generate Images
```bash
# Install PlantUML
brew install plantuml  # macOS
apt-get install plantuml  # Linux

# Generate all as PNG
cd diagrams
plantuml *.puml

# Generate as SVG (scalable)
plantuml -tsvg *.puml

# Generate as PDF (for printing)
plantuml -tpdf *.puml
```

---

## 📊 Diagram Statistics

| Category | Count | Total Size |
|----------|-------|------------|
| UML Diagrams | 9 | 38KB |
| Data Diagrams | 3 | 14KB |
| Architecture | 3 | 16KB |
| **Total Diagrams** | **15** | **68KB** |
| Documentation | 4 | 40KB |
| **Grand Total** | **19 files** | **108KB** |

---

## ✨ Diagram Highlights

### Most Comprehensive
🏆 **State Diagrams** (13) - Covers 4 different entity lifecycles

### Most Detailed
🏆 **Deployment Diagram** (14) - Complete infrastructure specification

### Best for Beginners
🏆 **System Architecture** (03) - Clear, visual, high-level overview

### Most Technical
🏆 **Detailed Database Schema** (11) - Every constraint, index, and relationship

### Best for Presentations
🏆 **Use Case Diagram** (04) - Shows all features and users clearly

---

## 🎨 Visual Examples

Each diagram type serves a specific purpose:

- **ERD/Schema** → "What data do we store?"
- **Class Diagram** → "How is the code organized?"
- **Architecture** → "What are the major components?"
- **Use Cases** → "What can users do?"
- **Sequences** → "How do processes work?"
- **State** → "What are the possible states?"
- **Deployment** → "Where does it run?"
- **Data Flow** → "How does data move?"
- **Activity** → "What's the complete workflow?"

---

## 🔧 Customization & Updates

### Want to Modify a Diagram?
1. Copy the .puml file content
2. Edit in text editor or online tool
3. Test the rendering
4. Save with a new name or update existing
5. Update this index if adding new diagrams

### Naming Convention
```
[Number]_[category]_[name].puml

Examples:
- 01_entity_relationship_diagram.puml
- 06_sequence_user_login.puml
- 14_deployment_diagram.puml
```

### Adding New Diagrams
1. Create with next sequential number
2. Follow naming convention
3. Update this INDEX.md
4. Update README.md
5. Update QUICK_REFERENCE.md
6. Update diagram count in all docs

---

## 📋 Checklist for Presentations

Using these diagrams for a presentation? Verify:

- [ ] Diagrams render correctly on your system
- [ ] You have backup images (PNG/PDF)
- [ ] You've reviewed PRESENTATION_GUIDE.md
- [ ] You know which diagrams to show
- [ ] You understand the flow between diagrams
- [ ] You can explain technical terms
- [ ] You have examples ready
- [ ] You've practiced timing
- [ ] You have notes for each diagram
- [ ] You're ready for Q&A

---

## 🆘 Troubleshooting

**Diagram won't render?**
- Check for unclosed brackets `{}`
- Verify PlantUML syntax
- Try online editor first
- Check file encoding (should be UTF-8)

**Can't find the right diagram?**
- Check QUICK_REFERENCE.md
- Use the "Find by..." sections above
- Search by keywords in README.md

**Need help with PlantUML?**
- Official docs: https://plantuml.com/
- Syntax guide: https://plantuml.com/guide
- Examples: https://real-world-plantuml.com/

---

## 📧 Contact & Support

Have questions or suggestions?
- Open an issue in the GitHub repository
- Contact the development team
- Submit a pull request with improvements

---

## 📜 License & Credits

**Project:** CIS320-Symvan-AI  
**Institution:** Indiana Wesleyan University  
**Purpose:** Campus event management system  
**Technology:** PHP, MySQL, HTML/CSS, OpenAI  
**Diagram Format:** PlantUML  
**Created:** December 2025  

---

## 🔗 Related Files

Outside this directory:
- `/setup/*.sql` - Database setup scripts
- `/frontend/*.php` - Application PHP files
- `/README.md` - Project README
- `/SETUP.md` - Setup instructions

---

**Happy Diagramming! 📊**

Last Updated: December 2025 | Version 1.0 | 15 Diagrams | 4 Guides

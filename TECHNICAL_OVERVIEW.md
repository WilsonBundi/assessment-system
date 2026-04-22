# TP Assessment System - Technical Overview

**Project Type:** Yii 2 Framework PHP Web Application
**Database:** PostgreSQL (tp_assessment)
**Purpose:** Teaching Practice (TP) E24 Assessment Management System

---

## 1. AUTHENTICATION SYSTEM

### Login Implementation

**Entry Point:** [site/index.php](views/site/index.php) - Login form and home page  
**LoginForm Model:** [models/LoginForm.php](models/LoginForm.php)

#### Authentication Flow:
1. **User Lookup:** `LoginForm::getUser()` searches by username or payroll number
   ```php
   // Accepts both username and payroll_no
   public static function findByIdentifier($identifier)
   ```

2. **Password Validation:** Triple-layer validation in [Users.php](models/Users.php)
   - Direct plaintext comparison (legacy support)
   - Yii Security hash verification (bcrypt)
   - Demo account fallback (admin/admin, demo/demo)

3. **Session Management:** Built-in Yii session handling
   - Config: [config/web.php](config/web.php) - component `user`
   - Identity Class: `app\models\Users` (implements `IdentityInterface`)
   - Auto-login: Configurable 30-day remember-me cookie

#### Current User Access:
```php
// From any controller/view
$user = Yii::$app->user->identity;  // Returns Users model
$userId = Yii::$app->user->id;      // Returns user_id

// Check authentication
if (Yii::$app->user->isGuest) { /* redirect */ }
```

---

## 2. USER ROLES & ACCESS CONTROL

### Role Architecture

**Role Model:** [models/Role.php](models/Role.php)  
**RBAC Helper:** [components/RbacHelper.php](components/RbacHelper.php)

#### Four Primary Roles:

| Role ID | Role Name | Description | Key Features |
|---------|-----------|-------------|--------------|
| 1 | **Supervisor** | Classroom teacher conducting assessments | Create/edit assessments, input grades, view own records |
| 2 | **Zone Coordinator** | District-level assessment validator | Review submitted assessments, validate completed work, view zone statistics |
| 3 | **TP Office** | Administrative assessment authority | System-wide reporting, data export, master data management, real-time dashboards |
| 4 | **Department Chair** | School leadership oversight | System-wide analytics, grade distribution reports, supervisor management, overall school statistics |

### Role Detection Methods

```php
// In RbacHelper::
RbacHelper::isSupervisor()         // Returns boolean
RbacHelper::isZoneCoordinator()    // Returns boolean
RbacHelper::isTpOffice()           // Returns boolean
RbacHelper::isDepartmentChair()    // Returns boolean

// Generic lookup
RbacHelper::hasRole('Supervisor')  // Check by name
RbacHelper::getUserRole($roleId)   // Get role name from ID
```

### Access Control Implementation

**Framework:** Yii2 `AccessControl` filter with `MatchCallback`

**Example:** [controllers/SupervisorController.php](controllers/SupervisorController.php)
```php
public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],  // Authenticated only
                    'matchCallback' => function($rule, $action) {
                        $user = Yii::$app->user->identity;
                        return $user && $user->role_id == 1;  // Supervisor check
                    }
                ],
            ],
        ],
    ];
}
```

### Data Visibility Filtering

**AssessmentController** filters based on role:
- **Supervisors:** Only see own assessments (`examiner_user_id == current_user_id`)
- **Zone Coordinators:** See assessments from schools in their zone
- **Department Chairs:** See assessments from their school
- **TP Office:** See all assessments system-wide

---

## 3. USER PROFILES

### User Model Structure

**File:** [models/Users.php](models/Users.php)

#### User Table Columns:
```
user_id (PK)
role_id (FK → role table)
username (unique)
password (supports plain + hashed)
payroll_no (optional, searchable)
name
phone
status
zone_id (FK → zone table, for Zone Coordinators)
school_id (FK → school table, for Department Chairs)
```

### Profile Management Controllers

**Supervisor Profile:** [controllers/SupervisorController.php](controllers/SupervisorController.php)
- **Actions:**
  - `actionProfile()` - Display profile with assessment statistics
  - `actionEdit()` - Edit profile information

- **Statistics Displayed:**
  - Total assessments conducted
  - Pending vs completed assessments
  - Number of schools assessed
  - Unique students evaluated
  - Total grades entered
  - Learning areas covered

**Zone Coordinator Profile:** [controllers/ZoneCoordinatorController.php](controllers/ZoneCoordinatorController.php)
- **Actions:**
  - `actionProfile()` - View validation workflow status
  - `actionEdit()` - Edit profile
  - `actionValidateAll()` - Bulk validation endpoint

- **Statistics:**
  - Total assessments across zone
  - Pending validations count
  - Validated assessments count
  - School count
  - Unique students assessed
  - Submitted assessments awaiting review
  - Recently validated assessments

**TP Office Profile:** [controllers/TpOfficeController.php](controllers/TpOfficeController.php)
- **Dashboard:** Comprehensive system statistics and real-time monitoring
- **AJAX Endpoint:** `actionGetDashboardData()` for live updates
- **Focus:** Validated assessments, reports, system health metrics

**Department Chair Profile:** [controllers/DepartmentChairController.php](controllers/DepartmentChairController.php)
- **Actions:**
  - `actionProfile()` - Overview dashboard
  - `actionEdit()` - Profile editing
  - `actionSystemReports()` - Comprehensive analytics

- **Key Metrics:**
  - Total/completed/in-progress assessments
  - Average scores
  - Schools and supervisor counts
  - Grade distribution analysis

**Generic User Profile:** [controllers/UsersController.php](controllers/UsersController.php)
- CRUD operations for user administration
- Generic profile view accessible to any authenticated user
- Assessment statistics tied to logged-in user

---

## 4. DATABASE MODELS

### Core Data Models

#### **Assessment** [models/Assessment.php](models/Assessment.php)
Central model for assessment records

```
assessment_id (PK)
examiner_user_id (FK → users)
student_reg_no
school_id (FK → school)
learning_area_id (FK → learning_area)
assessment_date
start_time, end_time
total_score (calculated from grades)
overall_level (BE/AE/ME/EE)
archived (0=active, 1=submitted)
archived_at (submission timestamp)
validated_by (FK → users, null until approved)
validated_at (approval timestamp)
```

**Relations:**
- `hasMany(Grade)` - Multiple grades per assessment
- `hasOne(Users)` - Examiner relationship
- `hasOne(Users)` - Validator relationship (validated_by)
- `hasOne(LearningArea)` - Learning domain
- `hasOne(School)` - School location

#### **Users** [models/Users.php](models/Users.php)
User account model (IdentityInterface implementation)

```
user_id (PK)
role_id (FK → role)
username (unique)
password
payroll_no
name
phone
status
zone_id (for Zone Coordinators)
school_id (for Department Chairs)
```

**Relations:**
- `hasMany(Assessment)` - Assessments created by user
- `hasOne(Role)` - User's role
- `hasOne(Zone)` - User's zone (if applicable)
- `hasOne(School)` - User's school (if applicable)

#### **Grade** [models/Grade.php](models/Grade.php)
Individual competency rating per assessment

```
grade_id (PK)
assessment_id (FK)
competence_id (FK)
level (BE/AE/ME/EE - grading scale)
score (0-10 numeric)
remarks (text feedback)
```

**TP E24 Grading Scale:**
- **BE (Below Expectations):** 0-3 points
- **AE (Approaching Expectations):** 4-5 points
- **ME (Meets Expectations):** 6-7 points
- **EE (Exceeds Expectations):** 8-10 points

**Static Methods:**
```php
Grade::getGradingScale()    // Returns scale definition
Grade::getValidLevels()     // Returns [BE, AE, ME, EE]
Grade::getLevelLabel($level) // Returns human-readable label
```

#### **School** [models/School.php](models/School.php)
```
school_id (PK)
school_code (unique)
school_name
zone_id (FK → zone)
```

#### **Zone** [models/Zone.php](models/Zone.php)
```
zone_id (PK)
zone_name
```

#### **Role** [models/Role.php](models/Role.php)
```
role_id (PK)
role_name (Supervisor/Zone Coordinator/TP Office/Department Chair)
```

#### **CompetenceArea** [models/CompetenceArea.php](models/CompetenceArea.php)
Assessment criteria/standards

```
competence_id (PK)
competence_name
description
```

**Relations:**
- `hasMany(Grade)` - Grades containing this competence
- `hasMany(Assessment)` - Via grades junction

#### **LearningArea** [models/LearningArea.php](models/LearningArea.php)
Curriculum domains (e.g., Mathematics, Language, etc.)

```
learning_area_id (PK)
learning_area_name
```

**Relations:**
- `hasMany(Assessment)` - Assessments in this area
- `hasMany(Strand)` - Sub-domains within this area

#### **Strand** [models/Strand.php](models/Strand.php)
Curriculum strand (subdivision of learning area)

```
strand_id (PK)
learning_area_id (FK)
name
```

**Relations:**
- `hasOne(LearningArea)` - Parent domain
- `hasMany(Substrand)` - Sub-components

#### **Substrand** [models/Substrand.php](models/Substrand.php)
Lowest level of curriculum organization

```
substrand_id (PK)
strand_id (FK)
name
```

### Search Models

Each major model has a corresponding `Search` class for filtering:
- [models/AssessmentSearch.php](models/AssessmentSearch.php)
- [models/UsersSearch.php](models/UsersSearch.php)
- [models/GradeSearch.php](models/GradeSearch.php)
- [models/RoleSearch.php](models/RoleSearch.php)
- [models/SchoolSearch.php](models/SchoolSearch.php)
- [models/ZoneSearch.php](models/ZoneSearch.php)
- [models/CompetenceAreaSearch.php](models/CompetenceAreaSearch.php)
- [models/LearningAreaSearch.php](models/LearningAreaSearch.php)
- [models/StrandSearch.php](models/StrandSearch.php)
- [models/SubstrandSearch.php](models/SubstrandSearch.php)

---

## 5. CONTROLLERS

### Main Controllers

#### **SiteController** [controllers/SiteController.php](controllers/SiteController.php)

**Actions:**
- `actionIndex()` - Login page/home redirect
- `actionDashboard()` - Role-adaptive dashboard with cached statistics
- `actionAbout()` - System information
- `actionContact()` - Contact form
- `actionError()` - Error page
- `actionLogout()` - Session termination (POST only)

**Features:**
- **Dashboard Stats Caching:** Session-based caching with timestamp validation
- **Role-Based Stats:**
  - Supervisors: Own assessments, schools, learning areas
  - Admins/coordinators: System-wide statistics
- **Recent Assessments:** Filtered display based on user role

#### **AssessmentController** [controllers/AssessmentController.php](controllers/AssessmentController.php)

**Actions:**
- `actionIndex()` - List assessments (role-filtered)
- `actionView($id)` - Display single assessment
- `actionCreate()` - New assessment form
- `actionUpdate($id)` - Edit assessment
- `actionDelete($id)` - Remove assessment (POST)
- `actionUploadImages($id)` - Image upload endpoint
- `actionGradeGrid($id)` - Competency grading interface
- `actionReportStudent($id)` - Student feedback report
- `actionReportOffice($id)` - Administrative report
- `actionAuditLog($id)` - Activity history
- `actionSaveGrid()` - Grade grid auto-save (AJAX)

**Access Control:** Authenticated users with role-based view filtering

**Features:**
- **Supervisor Ownership:** Auto-claims unassigned assessments
- **Report Generation:** Multi-format reports for different audiences
- **Audit Trails:** Full change tracking and logging
- **Grade Management:** Inline grid-based data entry

#### **SupervisorController** [controllers/SupervisorController.php](controllers/SupervisorController.php)

**Actions:**
- `actionProfile()` - View supervisor dashboard
- `actionEdit()` - Update profile information

**Dashboard Data:**
- Total/pending/completed assessments
- School and student counts
- Learning area coverage
- Recent assessment listing

#### **ZoneCoordinatorController** [controllers/ZoneCoordinatorController.php](controllers/ZoneCoordinatorController.php)

**Actions:**
- `actionProfile()` - Validation workflow dashboard
- `actionEdit()` - Profile management
- `actionValidateAll()` - Bulk validation trigger

**Features:**
- Workflow status displays (submitted vs. validated)
- Zone-specific filtering
- Validation tracking

#### **TpOfficeController** [controllers/TpOfficeController.php](controllers/TpOfficeController.php)

**Actions:**
- `actionIndex()` - Main dashboard with key metrics
- `actionGetDashboardData()` - AJAX real-time updates
- `actionGetReportsData()` - Report data refresh endpoint

**Real-Time Features:**
- Live assessment counters
- Status change detection
- Auto-updating dashboards

#### **DepartmentChairController** [controllers/DepartmentChairController.php](controllers/DepartmentChairController.php)

**Actions:**
- `actionProfile()` - Comprehensive overview
- `actionEdit()` - Profile updates
- `actionSystemReports()` - Analytics dashboard

**Analytics:**
- Grade distribution analysis
- Supervisor performance metrics
- School-wide statistics

#### **UsersController** [controllers/UsersController.php](controllers/UsersController.php)

**Actions:**
- CRUD: `actionIndex/View/Create/Update/Delete()`
- `actionProfile()` - Current user dashboard
- User management for administrative access

#### **ChatController** [controllers/ChatController.php](controllers/ChatController.php)

**Actions:**
- `actionSend()` - AJAX chat message handler

**Features:**
- Session-based chat history (last 20 messages)
- Error handling with fallback responses
- Assessment context awareness

---

## 6. VIEWS

### View Directory Structure

```
views/
├── layouts/
│   ├── main.php (Primary layout)
│   └── chat-widget.php (Chat interface)
├── site/
│   ├── index.php (Login/landing page)
│   ├── dashboard.php (Role-adaptive dashboard)
│   ├── login-guide.php (Help documentation)
│   ├── error.php (Error display)
│   ├── about.php, contact.php
├── assessment/
│   ├── index.php (Assessment list)
│   ├── view.php (Assessment detail)
│   ├── create.php, update.php (Forms)
│   ├── grade-grid.php (Competency entry)
│   ├── upload-images.php (Evidence upload)
│   ├── report.php (Report template)
│   ├── audit-log.php (Activity history)
│   ├── _form.php (Shared form partial)
│   ├── _search.php (Search filter)
│   └── _grade-color.php (Grade visualization)
├── supervisor/
│   ├── supervisor-profile.php
│   ├── edit-supervisor-profile.php
│   └── select-student.php
├── zone-coordinator/
│   ├── zone-coordinator-profile.php
│   └── edit-zone-coordinator-profile.php
├── tp-office/
│   ├── index.php (Dashboard)
│   └── _recent_assessments_table.php
├── department-chair/
│   ├── department-chair-profile.php
│   ├── edit-department-chair-profile.php
│   └── system-reports.php
├── users/ (CRUD views)
├── role/, school/, zone/, grade/ (Master data management)
└── [other controllers]/
```

### Key Layout Components

**main.php** - Primary layout
- Fixed navbar with gradient background (blue)
- Role-based navigation menu items
- Dynamic menu construction based on `Yii::$app->user->identity->role_id`
- Breadcrumb navigation
- Content section with padding
- Alert widget for flash messages

**chat-widget.php** - Floating chat assistant
- Fixed position chat bubble
- Message history display
- AJAX message submission to `ChatController::actionSend()`

---

## 7. KEY FEATURES

### 1. Assessment Management

**Creation Workflow:**
1. Supervisor initiates assessment for a student
2. Selects learning area and school
3. Enters assessment date, times, and student info
4. System auto-generates competency grade entries

**Tracking States:**
- `archived = 0` - In-progress assessment
- `archived = 1` - Submitted for review
- `validated_by = null` - Pending validation
- `validated_by = {user_id}` - Approved assessment

### 2. Grading System (TP E24 Template)

**Grade Entry:**
- Grid-based interface: [views/assessment/grade-grid.php](views/assessment/grade-grid.php)
- AJAX auto-save to prevent data loss
- Real-time validation against constraints
- Automatic total score calculation

**Grading Scale:**
```
BE (Below Expectations):      0-3 points → 0 grade_id
AE (Approaching Expectations): 4-5 points → 1 grade_id
ME (Meets Expectations):       6-7 points → 2 grade_id
EE (Exceeds Expectations):     8-10 points → 3 grade_id
```

### 3. Evidence Management

**Image Upload System:** [components/AssessmentImageBehavior.php](components/AssessmentImageBehavior.php)

**Specifications:**
- Storage: `web/uploads/assessments/{assessment_id}/`
- Max 5 images per assessment
- Supported formats: jpg, jpeg, png, gif, webp
- Maximum file size: 5MB
- Unique filename generation: `image_{timestamp}_{random}.{ext}`

**Upload Endpoint:** `assessment/upload-images`

### 4. Audit Logging

**System:** [components/AuditLogger.php](components/AuditLogger.php)

**Log Entries Include:**
- Timestamp (both human-readable and Unix)
- Action type (create, update, delete, submit, review, approve)
- Entity type and ID
- User ID, name, and role
- IP address
- Change details (before/after values)
- Additional notes

**Storage:** `runtime/audit-logs/{YYYY-MM-DD}_audit.log`
- JSON line format (one entry per line)
- Organized by date
- Retrievable by entity for audit trail

**API:**
```php
AuditLogger::log($action, $entityType, $entityId, $changes, $notes);
AuditLogger::getEntityLogs($entityType, $entityId, $days = 30);
AuditLogger::getRecentLogs($limit = 100);
```

### 5. Real-Time Chat Assistant

**Service:** [components/ChatService.php](components/ChatService.php)

**Capabilities:**
- TP E24 grading scale guidance
- Competency standards explanation
- Assessment methodology tips
- General knowledge responses (ChatGPT-style)
- Statistics queries ("How many students?")
- System help and workflow guidance

**Message Processing:**
1. Keyword detection for domain-specific queries
2. Context-aware response generation
3. Assessment-specific responses if assessment ID provided
4. Fallback to general knowledge for unclassified queries

**Session Integration:**
- Chat history stored in `Yii::$app->session['chatHistory']`
- Last 20 messages retained
- User context preserved across messages

### 6. Notification System

**Service:** [components/NotificationService.php](components/NotificationService.php)

**Notification Types:**
```php
const TYPE_ASSESSMENT_CREATED = 'assessment_created';
const TYPE_ASSESSMENT_UPDATED = 'assessment_updated';
const TYPE_ASSESSMENT_SUBMITTED = 'assessment_submitted';
const TYPE_GRADES_ADDED = 'grades_added';
const TYPE_FEEDBACK_READY = 'feedback_ready';
const TYPE_REVIEW_REQUIRED = 'review_required';
const TYPE_REPORT_GENERATED = 'report_generated';
const TYPE_STUDENT_SELECTED = 'student_selected';
const TYPE_EVIDENCE_UPLOADED = 'evidence_uploaded';
const TYPE_ASSESSMENT_COMPLETED = 'assessment_completed';
```

**Trigger Points:**
- Assessment lifecycle events (create, submit, validate)
- Grading completion
- Report generation
- Evidence uploads

### 7. Multi-Role Dashboards

**Adaptive Dashboard:** [views/site/dashboard.php](views/site/dashboard.php)

**Display Variations:**
- **Supervisor:** Personal assessment statistics
- **Zone Coordinator:** Validation queue and metrics
- **TP Office:** System-wide dashboards with real-time updates
- **Department Chair:** School analytics and performance insights

**Caching:** Dashboard stats cached in session with timestamp validation

### 8. Reporting

**Report Types:**

**StudentReport** `assessment/report-student`
- Individual student assessment summary
- Competency-level grades
- Written feedback
- Performance level (overall_level)

**OfficeReport** `assessment/report-office`
- Aggregated school/zone data
- Grade distribution analysis
- Submission status tracking

**SystemReports** (Department Chair)
- School-wide statistics
- Supervisor performance metrics
- Grade distribution charts

---

## 8. CONFIGURATION FILES

### Application Configuration

**Main Config:** [config/web.php](config/web.php)

#### Components Configuration:

**request**
```php
'cookieValidationKey' => 'T-9jIKyfNuocri1Bwa8p4hcX6_3F3xAH'
```

**user** (Authentication)
```php
'identityClass' => 'app\models\Users'
'enableAutoLogin' => true
```

**db** (Database Connection)
- Loaded from [config/db.php](config/db.php)

**cache**
```php
'class' => 'yii\caching\FileCache'
```

**log** (Logging)
```php
'targets' => [
    [
        'class' => 'yii\log\FileTarget',
        'levels' => ['error', 'warning'],
    ],
]
```

**mailer** (Email)
```php
'class' => \yii\symfonymailer\Mailer::class
'useFileTransport' => true
```

**urlManager** (Routing)
```php
'enablePrettyUrl' => true
'showScriptName' => false
// Custom rules for assessment routes
'assessment/view/<id:\d+>' => 'assessment/view'
'assessment/grade-grid/<id:\d+>' => 'assessment/grade-grid'
// ... etc
```

### Database Configuration

**File:** [config/db.php](config/db.php)

```php
[
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;port=5432;dbname=tp_assessment',
    'username' => 'postgres',
    'password' => 'kabish',
    'charset' => 'utf8',
]
```

**Database:** PostgreSQL
**Host:** localhost
**Port:** 5432
**Name:** tp_assessment

### Application Parameters

**File:** [config/params.php](config/params.php)

```php
[
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
]
```

### Environment-Specific Config

**Development Mode:**
- Debug Module enabled
- Gii (code generator) enabled
- Enhanced logging
- Schema cache disabled

---

## 9. COMPONENT CLASSES

### RbacHelper [components/RbacHelper.php](components/RbacHelper.php)

**Purpose:** Centralized role-based access control

**Key Methods:**
```php
// Role name lookup
static::hasRole($roleName) : bool
static::getUserRole($roleId) : string|null

// Specific role checks
static::isSupervisor() : bool
static::isZoneCoordinator() : bool
static::isTpOffice() : bool
static::isDepartmentChair() : bool
```

**Usage:**
```php
if (RbacHelper::isSupervisor()) {
    // Show supervisor-only content
}
```

### AuditLogger [components/AuditLogger.php](components/AuditLogger.php)

**Purpose:** Comprehensive activity logging and audit trail

**Log Structure:**
```json
{
    "timestamp": "2024-04-07 10:30:45",
    "action": "update",
    "entity_type": "assessment",
    "entity_id": 123,
    "user_id": 5,
    "user_name": "John Supervisor",
    "user_role": "Supervisor",
    "ip_address": "192.168.1.100",
    "changes": {"total_score": [75, 80]},
    "notes": "Grade adjustment",
    "timestamp_unix": 1712500245
}
```

**Public Methods:**
```php
static::log($action, $entityType, $entityId, $changes, $notes) : bool
static::getEntityLogs($entityType, $entityId, $days) : array
static::getRecentLogs($limit) : array
```

### ChatService [components/ChatService.php](components/ChatService.php)

**Purpose:** AI-powered chatbot for assessment guidance

**Processing Pipeline:**
1. Input validation and trimming
2. Greeting detection (hello, hi, hey, etc.)
3. Keyword-based topic routing:
   - TP E24 grading scale questions
   - Competency standards
   - Assessment methodology
   - Statistics/data queries
   - System help
   - General knowledge

**Response Methods:**
```php
public function generateResponse($message, $context, $user, $assessmentId) : string

// Internal routing methods:
private function gradeResponse($message) : string
private function competencyResponse($message) : string
private function assessmentResponse($message, $assessment) : string
private function helpResponse($message) : string
private function generalKnowledgeResponse($message, $user) : string
private function handleStatsQuery($message, $user) : string
```

**Context Awareness:**
- User name and role integration
- Assessment-specific guidance when assessment ID provided
- Learning area context from assessment

### NotificationService [components/NotificationService.php](components/NotificationService.php)

**Purpose:** Event-driven notification system

**Notification Constants:**
- `TYPE_ASSESSMENT_CREATED`
- `TYPE_ASSESSMENT_UPDATED`
- `TYPE_ASSESSMENT_SUBMITTED`
- `TYPE_GRADES_ADDED`
- `TYPE_FEEDBACK_READY`
- `TYPE_REVIEW_REQUIRED`
- `TYPE_REPORT_GENERATED`
- `TYPE_STUDENT_SELECTED`
- `TYPE_EVIDENCE_UPLOADED`
- `TYPE_ASSESSMENT_COMPLETED`

**Event Triggers:**
```php
NotificationService::notifyAssessmentCreated(Assessment)
NotificationService::notifyGradesComplete(Assessment)
NotificationService::notifyFeedbackReady(Assessment)
NotificationService::notifyReviewRequired(Assessment)
```

**Recipient Logic:**
- Notifications delivered to role-specific users
- Zone Coordinators notified for review tasks
- Supervisors notified of assessment events
- TP Office notified for system-wide events

### AssessmentImageBehavior [components/AssessmentImageBehavior.php](components/AssessmentImageBehavior.php)

**Purpose:** Evidence image management and validation

**Constraints:**
- Maximum 5 images per assessment
- Allowed formats: jpg, jpeg, png, gif, webp
- Maximum size: 5MB per image
- Storage: `/web/uploads/assessments/{assessment_id}/`

**Key Methods:**
```php
static::uploadImages(Assessment, UploadedFile[]) : array
static::getImages($assessmentId) : array
static::deleteImage($assessmentId, $filename) : bool
static::getUploadDir($assessmentId) : string
static::getUploadUrl($assessmentId) : string
```

**File Naming:** `image_{timestamp}_{randomInt}.{ext}`

**Usage Example:**
```php
$files = UploadedFile::getInstances($model, 'images');
$uploaded = AssessmentImageBehavior::uploadImages($assessment, $files);
```

---

## 10. IMPORTANT WIDGETS

### Chat Widget [views/layouts/chat-widget.php](views/layouts/chat-widget.php)

**Functionality:** Floating chatbot interface

**Features:**
- Fixed position on page
- Message history display
- Expandable/collapsible interface
- AJAX communication with ChatController
- Assessment context awareness

**Trigger:** Post message to `/chat/send` endpoint

### Navigation Menu

**Location:** [views/layouts/main.php](views/layouts/main.php)

**Dynamic Menu Items Based on Role:**

```php
// Supervisor (role_id = 1)
'Assessments' > 'Create Assessment', 'My Assessments', 'Assessment Search'

// Zone Coordinator (role_id = 2)
Navigation shows validation workflow items

// TP Office (role_id = 3)
'TP Office' > Dashboard, Reports, Master Data, Archive Management

// Department Chair (role_id = 4)
System Reports, School Management, Supervisor Oversight
```

### Alert Widget

**Component:** `yii\bootstrap5\Alert`

**Use:** Display flash messages (success, error, warning, info)

**Integration:**
```php
Yii::$app->session->setFlash('success', 'Action completed!');
// Automatically displayed in views via Alert widget
```

### Breadcrumb Navigation

**Component:** `yii\bootstrap5\Breadcrumbs`

**Dynamic Path Generation:** Automatically built from controller/action routing

---

## DATABASE MIGRATIONS

### Migration Files

**Location:** [migrations/](migrations/)

#### Archive System Migrations:
- `m260401_125702_add_archive_columns_to_assessment` - Adds `archived`, `archived_at` columns
- `m260401_130350_add_archived_columns_to_assessment` - Duplicate/alternative version
- `m260401_130406_add_archived_columns_to_assessment` - Alternative version

**Purpose:** Track assessment submission and validation workflow

#### Zone/School Assignment:
- `m260402_114038_add_zone_school_to_users` - Adds `zone_id`, `school_id` columns to users

**Purpose:** Support zone/school-based access control

#### Examiner/Validator Assignment:
- `m260403_110000_backfill_assessment_examiner_user_id` - Populates examiner relationship
- `m260403_add_validated_by_to_assessment` - Adds `validated_by`, `validated_at` columns

**Purpose:** Track who conducted and approved assessments

---

## APPLICATION BOOTSTRAP

**Bootstrap Process:**

1. **Entry Point:** `index.php` (front controller)
2. **Configuration Loading:** Loads appropriate config based on environment
   - Development: `config/test.php` or `config/web.php`
   - Production: `config/web.php`
3. **Database Connection:** PostgreSQL connection from `config/db.php`
4. **Component Initialization:** Services and helpers instantiated
5. **Router Processing:** Pretty URL routing via `urlManager`
6. **Access Control:** Applied at controller level via `AccessControl` filter
7. **Session Management:** Cookie-based with optional auto-login

---

## SECURITY FEATURES

### Password Management

**Dual Support:**
1. **Plaintext Check** (legacy/demo)
2. **Yii Security Hash** (bcrypt) - preferred
3. **Demo Account** - fallback for testing

**Validation Logic** ([models/Users.php](models/Users.php)):
```php
public function validatePassword($password)
{
    // Try plaintext
    if ($this->password === $password) return true;
    
    // Try bcrypt
    if (Yii::$app->security->validatePassword($password, $this->password)) 
        return true;
    
    // Try demo accounts
    if (($this->username === 'admin' && $password === 'admin') || 
        ($this->username === 'demo' && $password === 'demo')) 
        return true;
    
    return false;
}
```

### CSRF Protection

**Configuration:** Cookie validation key in `web.php`
**Meta Tags:** Auto-generated in main layout

### Access Control

**Multi-Layer Approach:**
1. Authentication check (logged-in user required)
2. Role verification (role_id matching)
3. Ownership verification (own assessments only for supervisors)
4. Zone/school verification (zone/school coordinators)

### RBAC (Role-Based Access Control)

**Implementation:** Yii2 `AccessControl` filter with custom `matchCallback`

**Role Hierarchy:**
- TP Office (role_id=3) - System-wide access
- Zone Coordinator (role_id=2) - Zone-level access
- Department Chair (role_id=4) - School-level access
- Supervisor (role_id=1) - Own records only

---

## DEVELOPMENT HELPERS

### Yii Debug Module

**Enabled in Development:**
- Accessible at `/_debug/`
- Database query inspection
- Log review
- Performance profiling

### Code Generation (Gii)

**Enabled in Development:**
- Model generation from existing tables
- CRUD scaffolding
- Form generation
- Accessible at `/gii/`

---

## KEY WORKFLOWS

### Assessment Submission Workflow

```
1. Supervisor creates Assessment (archived=0)
2. Supervisor enters grades via grade-grid
3. Supervisor uploads evidence images
4. Supervisor submits (archived=1)
5. Zone Coordinator validates (validated_by set, validated_at set)
6. TP Office generates reports and exports
7. Audit log captures all changes
```

### Real-Time Dashboard Updates

```
TP Office Dashboard (actionIndex) 
→ Polls actionGetDashboardData() via AJAX
→ Checks for assessments updated since last_update timestamp
→ Returns updated HTML snippets if new assessments found
→ Frontend updates assessment table dynamically
```

### Chat Assistance Workflow

```
User submits message via chat widget
→ POST to ChatController::actionSend()
→ ChatService::generateResponse() processes message
→ Keyword routing selects response type
→ Assessment context incorporated if available
→ Response returned as JSON
→ Chat history updated in session (max 20 messages)
→ Frontend displays response in chat UI
```

---

## PERFORMANCE CONSIDERATIONS

### Session Caching

**Dashboard Statistics:**
- Cached in `Yii::$app->session['dashboardStats']`
- Timestamp tracked in `dashboardStats_time`
- Prevents repeated database queries for analytics

### Query Optimization

**Active Query Usage:**
- Eager loading via `hasMany()`, `hasOne()`
- Select distinct only needed columns
- Limit result sets (e.g., `limit(20)` for recent items)

**Indexing Strategy:**
- Primary keys on all tables
- Foreign key indexes
- Consider indexes on:
  - `assessment.examiner_user_id`
  - `assessment.school_id`
  - `assessment.archived`
  - `grade.assessment_id`
  - `grade.competence_id`

---

## DEPLOYMENT NOTES

### Prerequisites

- PHP 7.4 + (Yii 2 framework requirement)
- PostgreSQL 10+
- Web server (Apache/Nginx with mod_rewrite)
- Writable directories:
  - `runtime/` - for logs and cache
  - `web/uploads/` - for image uploads
  - `runtime/audit-logs/` - for audit trail

### Configuration Requirements

1. Set database credentials in [config/db.php](config/db.php)
2. Update `cookieValidationKey` in [config/web.php](config/web.php) to random string
3. Update email settings in [config/params.php](config/params.php)
4. Run migrations: `php yii migrate`
5. Seed master data (competencies, zones, schools, roles)

### File Permissions

```bash
chmod 777 runtime/
chmod 777 runtime/audit-logs/
chmod 777 web/uploads/
chmod 777 web/uploads/assessments/
```

---

## TESTING UTILITIES

**Demo Scripts in Root:**
- `test_pdf.php` - PDF generation testing
- `test_chatbot.php` - Chat service testing
- `test_models.php` - Model relationship testing
- `test_user_roles.php` - RBAC verification
- `debug_chat.php`, `debug_chat2.php` - Chat debugging

**Database Inspection:**
- `temp_db_schema.php` - Display table structure
- `temp_db_inspect.php` - Database analysis
- `check_db.php` - Basic connectivity check

---

## SUMMARY TABLE

| Component | Location | Purpose |
|-----------|----------|---------|
| **Authentication** | LoginForm, Users | User login & session management |
| **RBAC** | RbacHelper | Role-based access control |
| **Assessments** | Assessment, AssessmentController | Core assessment tracking |
| **Grading** | Grade, grade-grid.php | TP E24 competency grading |
| **Audit** | AuditLogger | Activity logging and trails |
| **Chat** | ChatService, ChatController | AI-powered chatbot |
| **Images** | AssessmentImageBehavior | Evidence upload management |
| **Notifications** | NotificationService | Event-based notifications |
| **Reports** | Various controllers | Multi-format reporting |
| **Real-Time** | AJAX endpoints | Live dashboard updates |

---

## API ENDPOINTS SUMMARY

### Assessment Management
- `GET /assessment` - List assessments
- `GET /assessment/view/{id}` - View single assessment
- `POST /assessment/create` - Create assessment
- `POST /assessment/update/{id}` - Update assessment
- `POST /assessment/delete/{id}` - Delete assessment

### Grading
- `GET /assessment/grade-grid/{id}` - Grade entry interface
- `POST /assessment/save-grid` - Auto-save grades (AJAX)

### Evidence
- `POST /assessment/upload-images/{id}` - Upload evidence images

### Reports
- `GET /assessment/report-student/{id}` - Student report
- `GET /assessment/report-office/{id}` - Administrative report
- `GET /assessment/audit-log/{id}` - Activity history

### Chat
- `POST /chat/send` - Send chat message (AJAX)

### Dashboards
- `GET /site/dashboard` - Role-adaptive dashboard
- `POST /tp-office/get-dashboard-data` - Real-time stats (AJAX)
- `POST /tp-office/get-reports-data` - Report updates (AJAX)
- `GET /supervisor/profile` - Supervisor dashboard
- `GET /zone-coordinator/profile` - Zone coordinator dashboard
- `GET /department-chair/profile` - Chair dashboard

---

**Document Version:** 1.0  
**Last Updated:** April 7, 2026  
**System Status:** Production Ready

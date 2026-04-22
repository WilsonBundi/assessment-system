<?php
use yii\helpers\Html;

$this->title = 'Login Guide - TP Assessment System';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="login-guide">
    <style>
        .login-container {
            padding: 40px 0;
        }
        .actor-card {
            margin-bottom: 30px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .actor-header {
            padding: 20px;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
        .actor-body {
            padding: 30px;
            background: white;
        }
        .role-icon {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        .credentials-box {
            background: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin: 15px 0;
            border-radius: 5px;
        }
        .credentials-box code {
            display: block;
            font-size: 1.1rem;
            font-weight: bold;
            color: #333;
            margin: 5px 0;
        }
        .workflow-step {
            display: flex;
            margin-bottom: 15px;
            background: #f0f7ff;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .workflow-number {
            background: #007bff;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            flex-shrink: 0;
        }
        .supervisor-header { background: #007bff; }
        .zone-header { background: #17a2b8; }
        .chair-header { background: #ffc107; }
        .tp-header { background: #dc3545; }
        .action-link {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .action-link-primary { background: #007bff; color: white; }
        .action-link-info { background: #17a2b8; color: white; }
        .action-link-warning { background: #ffc107; color: #333; }
        .action-link-danger { background: #dc3545; color: white; }
    </style>

    <div class="login-container">
        <div class="row mb-5">
            <div class="col-md-12">
                <div style="text-align: center; padding: 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px;">
                    <h1 style="font-size: 2.5rem; margin-bottom: 10px;">TP Assessment System</h1>
                    <p style="font-size: 1.2rem;">Complete Login & Access Guide for All Actors</p>
                </div>
            </div>
        </div>

        <!-- SUPERVISOR -->
        <div class="row">
            <div class="col-md-12">
                <div class="actor-card">
                    <div class="actor-header supervisor-header">
                        <i class="fas fa-user-tie"></i> SUPERVISOR (Teaching and Learning)
                    </div>
                    <div class="actor-body">
                        <h4>Role Overview</h4>
                        <p>Supervisors are responsible for creating assessments, grading students on 12 competence areas, uploading evidence images, and submitting assessment reports.</p>

                        <h5>Login Credentials</h5>
                        <div class="credentials-box">
                            <strong>Username:</strong> <code>supervisor1</code><br>
                            <strong>Password:</strong> <code>password123</code>
                        </div>

                        <h5>Key Responsibilities</h5>
                        <ul>
                            <li>✓ Create new assessments for students</li>
                            <li>✓ Grade students on 12 competence areas</li>
                            <li>✓ Upload evidence images (max 5 images)</li>
                            <li>✓ Auto-fill remarks when forgotten</li>
                            <li>✓ Submit completed assessment reports</li>
                            <li>✓ View own assessment history</li>
                        </ul>

                        <h5>Step-by-Step Workflow</h5>
                        <div class="workflow-step">
                            <div class="workflow-number">1</div>
                            <div><strong>Login</strong> - Enter your credentials on the login page</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">2</div>
                            <div><strong>Dashboard</strong> - View your personal dashboard with statistics</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">3</div>
                            <div><strong>Select Student</strong> - Click "Create Assessment" → Select a student</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">4</div>
                            <div><strong>Grade Grid</strong> - Enter scores (0-10) for 12 competencies</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">5</div>
                            <div><strong>Upload Evidence</strong> - Add up to 5 images as proof</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">6</div>
                            <div><strong>Submit Report</strong> - Complete and submit the assessment</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">7</div>
                            <div><strong>View Profile</strong> - Check Supervisor Profile for statistics</div>
                        </div>

                        <p>
                            <?= Html::a('→ Go to Supervisor Profile', ['/supervisor/profile'], ['class' => 'action-link action-link-primary']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ZONE COORDINATOR -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="actor-card">
                    <div class="actor-header zone-header">
                        <i class="fas fa-user-check"></i> ZONE COORDINATOR (Quality Assurance)
                    </div>
                    <div class="actor-body">
                        <h4>Role Overview</h4>
                        <p>Zone Coordinators review and validate assessment reports submitted by supervisors. They can make corrections and ensure quality and completeness before final approval.</p>

                        <h5>Login Credentials</h5>
                        <div class="credentials-box">
                            <strong>Username:</strong> <code>coordinator1</code><br>
                            <strong>Password:</strong> <code>password123</code>
                        </div>

                        <h5>Key Responsibilities</h5>
                        <ul>
                            <li>✓ Review all assessment reports</li>
                            <li>✓ Validate completed assessments</li>
                            <li>✓ Edit assessment details for accuracy</li>
                            <li>✓ Ensure all competencies are graded</li>
                            <li>✓ Approve and notify supervisors</li>
                            <li>✓ Monitor assessment quality</li>
                        </ul>

                        <h5>Step-by-Step Workflow</h5>
                        <div class="workflow-step">
                            <div class="workflow-number">1</div>
                            <div><strong>Login</strong> - Enter your credentials</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">2</div>
                            <div><strong>Coordinator Dashboard</strong> - See pending validations</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">3</div>
                            <div><strong>Review Assessment</strong> - View supervisor's submitted report</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">4</div>
                            <div><strong>Edit if Needed</strong> - Correct any errors or incomplete data</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">5</div>
                            <div><strong>Validate</strong> - Formally approve the assessment</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">6</div>
                            <div><strong>Supervisor Notified</strong> - Assessment approval notification sent</div>
                        </div>

                        <p>
                            <?= Html::a('→ Go to Zone Coordinator Profile', ['/zone-coordinator/profile'], ['class' => 'action-link action-link-info']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DEPARTMENT CHAIR -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="actor-card">
                    <div class="actor-header chair-header">
                        <i class="fas fa-crown"></i> DEPARTMENT CHAIR (System Monitoring)
                    </div>
                    <div class="actor-body">
                        <h4>Role Overview</h4>
                        <p>Department Chairs monitor all TP assessments system-wide and view comprehensive reports on assessment trends, completion rates, and system performance.</p>

                        <h5>Login Credentials</h5>
                        <div class="credentials-box">
                            <strong>Username:</strong> <code>chair1</code><br>
                            <strong>Password:</strong> <code>password123</code>
                        </div>

                        <h5>Key Responsibilities</h5>
                        <ul>
                            <li>✓ Monitor all TP assessments</li>
                            <li>✓ View system-wide reports</li>
                            <li>✓ Analyze grade distributions</li>
                            <li>✓ Track completion rates</li>
                            <li>✓ Oversee system performance</li>
                            <li>✓ Review competency trends</li>
                        </ul>

                        <h5>Step-by-Step Workflow</h5>
                        <div class="workflow-step">
                            <div class="workflow-number">1</div>
                            <div><strong>Login</strong> - Enter your credentials</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">2</div>
                            <div><strong>Dashboard</strong> - See system-wide statistics and overview</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">3</div>
                            <div><strong>View Reports</strong> - Access comprehensive system reports</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">4</div>
                            <div><strong>Monitor Assessments</strong> - Review all assessments in the system</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">5</div>
                            <div><strong>Analyze Trends</strong> - View grade distribution and statistics</div>
                        </div>

                        <p>
                            <?= Html::a('→ Go to Department Chair Profile', ['/department-chair/profile'], ['class' => 'action-link action-link-warning']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- TP OFFICE -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="actor-card">
                    <div class="actor-header tp-header">
                        <i class="fas fa-building"></i> TP OFFICE (System Administration)
                    </div>
                    <div class="actor-body">
                        <h4>Role Overview</h4>
                        <p>TP Office users manage the entire system infrastructure including users, schools, zones, grades, competence areas, and assessment reports. They ensure proper system configuration and data integrity.</p>

                        <h5>Login Credentials</h5>
                        <div class="credentials-box">
                            <strong>Username:</strong> <code>tpoffice1</code><br>
                            <strong>Password:</strong> <code>password123</code>
                        </div>

                        <h5>Key Responsibilities</h5>
                        <ul>
                            <li>✓ Manage all system users</li>
                            <li>✓ Configure schools and zones</li>
                            <li>✓ Setup grade levels</li>
                            <li>✓ Define competence areas</li>
                            <li>✓ Create learning areas, strands, substrands</li>
                            <li>✓ View and archive assessment records</li>
                            <li>✓ Generate system reports</li>
                            <li>✗ CANNOT upload or delete evidence images</li>
                        </ul>

                        <h5>Step-by-Step Workflow</h5>
                        <div class="workflow-step">
                            <div class="workflow-number">1</div>
                            <div><strong>Login</strong> - Enter your credentials</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">2</div>
                            <div><strong>TP Office Dashboard</strong> - See system overview</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">3</div>
                            <div><strong>Master Data Configuration</strong> - Setup schools, zones, grades, competencies</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">4</div>
                            <div><strong>User Management</strong> - Create and manage system users with roles</div>
                        </div>
                        <div class="workflow-step">
                            <div class="workflow-number">5</div>
                            <div><strong>View Assessment Reports</strong> - Monitor and archive records</div>
                        </div>

                        <p>
                            <?= Html::a('→ Go to TP Office Profile', ['/tp-office-coordinator/profile'], ['class' => 'action-link action-link-danger']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- COMMUNICATION FLOW -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0"><i class="fas fa-exchange-alt"></i> Communication & Data Flow</h4>
                    </div>
                    <div class="card-body">
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 400'%3E%3Cdefs%3E%3Cstyle%3E.box%7Bfill:%23f0f0f0;stroke:%23333;stroke-width:2%7D.text%7Bfont-size:14px;text-anchor:middle%7D.arrow%7Bfill:none;stroke:%23007bff;stroke-width:2;marker-end:url(%23arrowhead)%7D%3C/defs%3E%3Cmarker id='arrowhead' markerWidth='10' markerHeight='10' refX='9' refY='3' orient='auto'%3E%3Cpolygon points='0 0, 10 3, 0 6' fill='%23007bff'/%3E%3C/marker%3E%3Crect class='box' x='30' y='100' width='120' height='80'/%3E%3Ctext class='text' x='90' y='140'%3ESUPERVISOR%3C/text%3E%3Ctext class='text' x='90' y='160'%3E(Creates)%3C/text%3E%3Crect class='box' x='220' y='100' width='120' height='80'/%3E%3Ctext class='text' x='280' y='140'%3ECOORDINATOR%3C/text%3E%3Ctext class='text' x='280' y='160'%3E(Validates)%3C/text%3E%3Crect class='box' x='410' y='100' width='120' height='80'/%3E%3Ctext class='text' x='470' y='140'%3ECHAIR%3C/text%3E%3Ctext class='text' x='470' y='160'%3E(Monitors)%3C/text%3E%3Crect class='box' x='600' y='100' width='120' height='80'/%3E%3Ctext class='text' x='660' y='140'%3ETP OFFICE%3C/text%3E%3Ctext class='text' x='660' y='160'%3E(Manages)%3C/text%3E%3Cpath class='arrow' d='M 150 140 L 220 140'/%3E%3Cpath class='arrow' d='M 340 140 L 410 140'/%3E%3Cpath class='arrow' d='M 530 140 L 600 140'/%3E%3C/svg%3E" alt="Communication Flow" style="width: 100%; max-width: 800px; margin: 20px auto; display: block;">

                        <h5 className="mt-4">Key Communication Points</h5>
                        <div class="alert alert-info" role="alert">
                            <strong>1. Assessment Submission:</strong> Supervisor creates & submits assessment → Zone Coordinator gets notified
                        </div>
                        <div class="alert alert-info" role="alert">
                            <strong>2. Validation Process:</strong> Zone Coordinator reviews & validates → Supervisor receives validation notification
                        </div>
                        <div class="alert alert-info" role="alert">
                            <strong>3. System Oversight:</strong> Department Chair monitors all assessments system-wide
                        </div>
                        <div class="alert alert-info" role="alert">
                            <strong>4. Administration:</strong> TP Office manages users, schools, competencies, and system configuration
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QUICK LINKS -->
        <div class="row mt-5 mb-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="fas fa-link"></i> Quick Access Links</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <p><strong>For Supervisors:</strong></p>
                                <p><?= Html::a('• Create Assessment', ['/assessment/create']) ?></p>
                                <p><?= Html::a('• My Profile', ['/supervisor/profile']) ?></p>
                                <p><?= Html::a('• View Assessments', ['/assessment/index']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>For Coordinators:</strong></p>
                                <p><?= Html::a('• My Profile', ['/zone-coordinator/profile']) ?></p>
                                <p><?= Html::a('• Review Assessments', ['/assessment/index']) ?></p>
                                <p><?= Html::a('• Validate Reports', ['/zone-coordinator/profile']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>For Department Chair:</strong></p>
                                <p><?= Html::a('• My Profile', ['/department-chair/profile']) ?></p>
                                <p><?= Html::a('• System Reports', ['/department-chair/system-reports']) ?></p>
                                <p><?= Html::a('• Monitor All', ['/department-chair/monitor-assessments']) ?></p>
                            </div>
                            <div class="col-md-3">
                                <p><strong>For TP Office:</strong></p>
                                <p><?= Html::a('• My Profile', ['/tp-office-coordinator/profile']) ?></p>
                                <p><?= Html::a('• Master Data', ['/tp-office-coordinator/master-data']) ?></p>
                                <p><?= Html::a('• Manage Users', ['/users/index']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BACK TO HOME -->
        <div class="text-center mb-5">
            <?= Html::a('← Back to Home', ['site/index'], ['class' => 'btn btn-outline-primary btn-lg']) ?>
        </div>
    </div>
</div>

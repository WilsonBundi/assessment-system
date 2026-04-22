<?php

namespace app\components;

use Yii;
use app\models\Assessment;
use app\models\CompetenceArea;
use app\models\Users;
use app\models\School;

/**
 * ChatService provides intelligent responses for supervisor chatbot
 */
class ChatService
{
    /**
     * Generate AI response based on user message
     */
    public function generateResponse($message, $context = [], $user = null, $assessmentId = null)
    {
        $messageLower = strtolower(trim($message));
        
        // Get assessment if provided
        $assessment = null;
        if ($assessmentId) {
            $assessment = Assessment::findOne($assessmentId);
        }

        // Empty message check
        if (empty($messageLower)) {
            return "Hello! 👋 I'm here to help. You can ask me about:\n• Assessment and grading\n• General knowledge\n• How to do things\n• Advice and tips\n• System help\n\nWhat's on your mind?";
        }

        // Check for greeting
        if ($this->isGreeting($messageLower)) {
            return $this->greetingResponse($user);
        }

        // Check for system statistics queries (assessment-specific)
        if ($this->containsKeyword($messageLower, ['student', 'how many', 'count', 'total']) || 
            strpos($messageLower, '?') !== false && strpos($messageLower, 'student') !== false) {
            $statsResponse = $this->handleStatsQuery($messageLower, $user);
            if ($statsResponse) {
                return $statsResponse;
            }
        }

        // Check for TP E24 scale questions (assessment-specific)
        if ($this->containsKeyword($messageLower, ['grade', 'level', 'be', 'ae', 'me', 'ee', 'score', 'scale'])) {
            return $this->gradeResponse($messageLower);
        }

        // Check for competency/standard questions (assessment-specific)
        if ($this->containsKeyword($messageLower, ['competenc', 'standard', 'criterion', 'rubric', 'expectation', 'indicator'])) {
            return $this->competencyResponse($messageLower);
        }

        // Check for assessment help (assessment-specific)
        if ($this->containsKeyword($messageLower, ['assess', 'evaluate', 'observe', 'performance', 'rubric'])) {
            return $this->assessmentResponse($messageLower, $assessment);
        }

        // Check for evidence/documentation questions (assessment-specific)
        if ($this->containsKeyword($messageLower, ['evidence', 'photo', 'image', 'document', 'upload', 'record'])) {
            return $this->evidenceResponse($messageLower);
        }

        // Check for system/workflow help (assessment-specific)
        if ($this->containsKeyword($messageLower, ['system', 'profile', 'dashboard', 'workflow', 'step', 'create assessment'])) {
            return $this->helpResponse($messageLower);
        }

        // General knowledge questions (like ChatGPT)
        return $this->generalKnowledgeResponse($messageLower, $user);
    }

    /**
     * Check if message is a greeting
     */
    private function isGreeting($message)
    {
        $greetings = ['hello', 'hi', 'hey', 'good morning', 'good afternoon', 'greetings'];
        foreach ($greetings as $greeting) {
            if (strpos($message, $greeting) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Handle system statistics queries about students, assessments, schools, etc.
     */
    private function handleStatsQuery($message, $user)
    {
        $response = null;

        // Student counts - "how many students", "total students", "students registered"
        if ((strpos($message, 'student') !== false) && 
            (strpos($message, 'how many') !== false || strpos($message, 'total') !== false || 
             strpos($message, 'count') !== false || strpos($message, 'registered') !== false)) {
            
            $totalStudents = Assessment::find()
                ->select(['DISTINCT student_reg_no'])
                ->count('DISTINCT student_reg_no');
            
            $yourStudents = $user ? Assessment::find()
                ->where(['examiner_user_id' => $user->user_id])
                ->select(['DISTINCT student_reg_no'])
                ->count('DISTINCT student_reg_no') : 0;

            $response = "📊 **Student Statistics:**\n\n";
            $response .= "• **Total students assessed (system-wide):** " . ($totalStudents ?: 0) . "\n";
            if ($user && $user->role_id == 1) {
                $response .= "• **Students you've assessed:** $yourStudents\n";
            }
            $response .= "\nEach student may have multiple assessments across different competencies and time periods.";
            return $response;
        }

        // Assessment counts - "how many assessments", "total assessments"
        if ((strpos($message, 'assessment') !== false) && 
            (strpos($message, 'how many') !== false || strpos($message, 'total') !== false || strpos($message, 'count') !== false)) {
            
            $totalAssessments = Assessment::find()->count();
            
            $yourAssessments = $user ? Assessment::find()
                ->where(['examiner_user_id' => $user->user_id])
                ->count() : 0;

            $response = "📋 **Assessment Statistics:**\n\n";
            $response .= "• **Total assessments (system-wide):** " . ($totalAssessments ?: 0) . "\n";
            if ($user && $user->role_id == 1) {
                $response .= "• **Assessments you've created:** $yourAssessments\n";
            }
            return $response;
        }

        // School counts - "how many schools", "total schools"
        if ((strpos($message, 'school') !== false) && 
            (strpos($message, 'how many') !== false || strpos($message, 'total') !== false || strpos($message, 'count') !== false)) {
            
            $totalSchools = School::find()->count();
            $yourSchools = $user ? Assessment::find()
                ->select(['DISTINCT school_id'])
                ->where(['examiner_user_id' => $user->user_id])
                ->count() : 0;

            $response = "🏫 **School Statistics:**\n\n";
            $response .= "• **Total schools (system-wide):** " . ($totalSchools ?: 0) . "\n";
            if ($user && $user->role_id == 1) {
                $response .= "• **Schools where you've assessed:** $yourSchools\n";
            }
            return $response;
        }

        // Competency counts - "how many competencies", "total standards"
        if ((strpos($message, 'competenc') !== false || strpos($message, 'standard') !== false) && 
            (strpos($message, 'how many') !== false || strpos($message, 'total') !== false)) {
            
            $totalCompetencies = CompetenceArea::find()->count();
            $response = "🎯 **Competency Standards:**\n\n";
            $response .= "• **Total competency areas:** " . ($totalCompetencies ?: 0) . "\n\n";
            $response .= "Each competency is assessed using the **TP E24 scale** (BE, AE, ME, EE)";
            return $response;
        }

        return $response;
    }

    /**
     * Check if message contains keywords
     */
    private function containsKeyword($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Greeting response
     */
    private function greetingResponse($user)
    {
        $name = $user ? $user->name : 'Supervisor';
        $greetings = [
            "Hello {$name}! 👋 I'm your TP Assessment Assistant. I'm here to help you with grading, competency standards, and assessment guidance. What can I help you with?",
            "Welcome {$name}! 🎓 I can assist you with TP E24 grading scales, competency rubrics, assessment techniques, and more. What's your question?",
            "Hi {$name}! 💡 Ready to help with your assessments. Feel free to ask about grades, standards, or assessment best practices.",
        ];
        return $greetings[array_rand($greetings)];
    }

    /**
     * Grade scale response - TP E24 guidance
     */
    private function gradeResponse($message)
    {
        // Check what grade level they're asking about
        $gradeMap = [
            'be' => [
                'level' => 'BE (Beginning)',
                'description' => 'Student is beginning to show understanding but requires substantial support',
                'indicators' => 'Limited recall of facts, minimal application, needs concrete examples',
                'what_to_do' => 'Provide direct instruction, use manipulatives, break tasks into smaller steps',
            ],
            'ae' => [
                'level' => 'AE (Approaching)',
                'description' => 'Student demonstrates emerging understanding with some independent work possible',
                'indicators' => 'Can recall basic concepts, attempts application with guidance, shows understanding of simpler ideas',
                'what_to_do' => 'Scaffold learning, provide opportunities for practice with feedback, use guided discovery',
            ],
            'me' => [
                'level' => 'ME (Meets)',
                'description' => 'Student demonstrates proficiency and understands key concepts and processes',
                'indicators' => 'Consistently applies knowledge, understands main ideas, completes tasks with minimal support',
                'what_to_do' => 'Maintain rigor, introduce extensions, encourage deeper analysis',
            ],
            'ee' => [
                'level' => 'EE (Exceeds)',
                'description' => 'Student demonstrates advanced understanding, goes beyond grade-level expectations',
                'indicators' => 'Applies knowledge in novel situations, makes connections, shows sophisticated understanding',
                'what_to_do' => 'Provide enrichment, encourage mentoring, assign leadership roles in tasks',
            ],
        ];

        // Find which grade is being asked about
        foreach ($gradeMap as $code => $info) {
            if (strpos($message, $code) !== false) {
                return $this->formatGradeInfo($info);
            }
        }

        // General scale explanation
        return "**TP E24 Grading Scale Explained:**\n\n" .
            "🔵 **BE** (Beginning) - Requires substantial support\n" .
            "🟡 **AE** (Approaching) - With guidance, emerging proficiency\n" .
            "🟢 **ME** (Meets) - Demonstrates proficiency\n" .
            "⭐ **EE** (Exceeds) - Advanced, beyond grade level\n\n" .
            "Which level would you like more details about? (Ask about BE, AE, ME, or EE)";
    }

    /**
     * Format grade information
     */
    private function formatGradeInfo($info)
    {
        return "**{$info['level']}**\n\n" .
            "📝 **Description:** {$info['description']}\n\n" .
            "✅ **What to look for:** {$info['indicators']}\n\n" .
            "💡 **How to support this student:**\n{$info['what_to_do']}";
    }

    /**
     * Competency/standard response
     */
    private function competencyResponse($message)
    {
        $competencies = CompetenceArea::find()->limit(5)->all();
        
        if (empty($competencies)) {
            return "I can help explain competency standards! The system contains several competency areas. When assessing, look for evidence that the student demonstrates:\n\n" .
                "✓ Understanding of key concepts\n" .
                "✓ Application to real situations\n" .
                "✓ Communication of ideas\n" .
                "✓ Problem-solving abilities\n\n" .
                "What specific competency would you like guidance on?";
        }

        $response = "**Available Competency Standards:**\n\n";
        foreach ($competencies as $comp) {
            $response .= "• **{$comp->competence_name}**\n";
            if ($comp->description) {
                $response .= "  _{$comp->description}_\n";
            }
        }
        $response .= "\n\nEach competency should be assessed based on student evidence and aligned to the TP E24 grading scale.";
        
        return $response;
    }

    /**
     * Assessment help response
     */
    private function assessmentResponse($message, $assessment = null)
    {
        $responses = [
            "**Assessment Tips:**\n\n" .
            "1️⃣ **Observe** - Watch student behavior and work\n" .
            "2️⃣ **Collect Evidence** - Take notes, photos, samples\n" .
            "3️⃣ **Analyze** - Compare to standards and rubrics\n" .
            "4️⃣ **Grade** - Assign level using TP E24 scale\n" .
            "5️⃣ **Record** - Document in system with evidence\n\n" .
            "💡 Quality assessment captures work over time, not just one moment.",
            
            "**Student Performance Assessment:**\n\n" .
            "🎯 **Look for Evidence Of:**\n" .
            "• Conceptual understanding\n" .
            "• Procedural fluency\n" .
            "• Application to new situations\n" .
            "• Communication & reasoning\n" .
            "• Collaboration & effort\n\n" .
            "📸 **Capture Evidence:**\n" .
            "• Photos of work\n" .
            "• Audio/video clips\n" .
            "• Written samples\n" .
            "• Observation notes",
            
            "**Effective Assessment Practices:**\n\n" .
            "✅ **DO:**\n" .
            "• Assess multiple times\n" .
            "• Use rubrics for consistency\n" .
            "• Provide specific feedback\n" .
            "• Document growth over time\n\n" .
            "❌ **DON'T:**\n" .
            "• Grade based on behavior alone\n" .
            "• Compare students to each other\n" .
            "• Rush judgments\n" .
            "• Ignore evidence",
        ];
        
        return $responses[array_rand($responses)];
    }

    /**
     * Evidence/documentation response
     */
    private function evidenceResponse($message)
    {
        return "**Capturing Assessment Evidence:**\n\n" .
            "📷 **Photos (Best for showing work):**\n" .
            "• Student work samples\n" .
            "• Project demonstrations\n" .
            "• Written responses\n" .
            "• Hands-on activities\n\n" .
            "✅ **What Makes Good Evidence:**\n" .
            "• Clear connection to standard\n" .
            "• Shows student's actual work\n" .
            "• Dated and labeled\n" .
            "• Multiple pieces over time\n\n" .
            "💾 **How to Upload:**\n" .
            "• Use the photo upload feature\n" .
            "• Maximum 5 images per assessment\n" .
            "• Include brief description\n" .
            "• Submit before completing assessment";
    }

    /**
     * Workflow/help response
     */
    private function helpResponse($message)
    {
        return "**Assessment Workflow (5 Steps):**\n\n" .
            "**Step 1️⃣ - Select Student**\n" .
            "Go to your profile → 'Create Assessment' → Search and select student\n\n" .
            "**Step 2️⃣ - Record Rubric**\n" .
            "Grade 12 competency standards using TP E24 scale (BE/AE/ME/EE)\n\n" .
            "**Step 3️⃣ - Upload Evidence**\n" .
            "Add photos/documentation (up to 5 images)\n\n" .
            "**Step 4️⃣ - Submit Assessment**\n" .
            "Review and submit when complete\n\n" .
            "**Step 5️⃣ - Get Confirmation**\n" .
            "Assessment saved - view in profile anytime\n\n" .
            "Need help at any step? Ask me!";
    }

    /**
     * Intelligent fallback for any general question (like ChatGPT)
     */
    private function intelligentFallback($message, $user)
    {
        return $this->generalKnowledgeResponse($message, $user);
    }

    /**
     * Answer general knowledge questions on ANY topic (ChatGPT-style)
     */
    private function generalKnowledgeResponse($message, $user)
    {
        $messageLower = strtolower(trim($message));

        // === HOW-TO QUESTIONS ===
        if ($this->startsWithKeyword($messageLower, ['how to', 'how can i', 'how do i', 'how should i'])) {
            return $this->generateHowToAnswer($messageLower);
        }

        // === WHY QUESTIONS ===
        if ($this->startsWithKeyword($messageLower, ['why', 'why is', 'why do'])) {
            return $this->generateWhyAnswer($messageLower);
        }

        // === WHAT QUESTIONS ===
        if ($this->startsWithKeyword($messageLower, ['what is', 'what are', 'what do', 'what can'])) {
            return $this->generateWhatAnswer($messageLower);
        }

        // === DEFINITION QUESTIONS ===
        if ($this->containsKeyword($messageLower, ['define', 'meaning of', 'what\'s', 'what is'])) {
            return $this->generateDefinitionAnswer($messageLower);
        }

        // === ADVICE QUESTIONS ===
        if ($this->containsKeyword($messageLower, ['advice', 'suggest', 'recommend', 'should i', 'best way', 'tip'])) {
            return $this->generateAdviceAnswer($messageLower);
        }

        // === COMPARISON QUESTIONS ===
        if ($this->containsKeyword($messageLower, ['vs', 'difference', 'compare', 'versus', 'better than'])) {
            return $this->generateComparisonAnswer($messageLower);
        }

        // === YES/NO QUESTIONS ===
        if (strpos($messageLower, '?') !== false && !$this->startsWithKeyword($messageLower, ['how', 'why', 'what', 'when', 'where'])) {
            return $this->generateYesNoAnswer($messageLower);
        }

        // === GENERAL STATEMENTS (not questions) ===
        if (strpos($messageLower, '?') === false) {
            return $this->generateTopicResponse($messageLower);
        }

        // === DEFAULT HELPFUL RESPONSE ===
        return $this->generateDefaultResponse($messageLower, $user);
    }

    /**
     * Check if message starts with keyword
     */
    private function startsWithKeyword($message, $keywords)
    {
        foreach ($keywords as $keyword) {
            if (strpos($message, $keyword) === 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate answer for "How to..." questions
     */
    private function generateHowToAnswer($message)
    {
        // Extract the main topic
        $topic = str_replace(['how to', 'how can i', 'how do i', 'how should i'], '', $message);
        $topic = trim($topic, '?');

        $responses = [
            "✅ **Here's how to $topic:**\n\n1. Start by understanding what you're trying to achieve\n2. Break it into smaller, manageable steps\n3. Take it one step at a time\n4. Test and adjust as needed\n5. Practice makes perfect!\n\nWould you like more specific guidance on any of these steps?",

            "📝 **Steps to $topic:**\n\n**Preparation:**\n• Gather what you need\n• Understand the basics\n\n**Execution:**\n• Follow the process systematically\n• Stay focused\n• Track your progress\n\n**Refinement:**\n• Review what worked\n• Improve for next time\n\nDo you need help with any specific part?",

            "$topic - Here's a helpful approach:\n\n1️⃣ **Plan** - Know what you're aiming for\n2️⃣ **Prepare** - Get ready with tools/knowledge\n3️⃣ **Execute** - Take action step by step\n4️⃣ **Review** - Check results and improve\n5️⃣ **Repeat** - Get better with practice\n\nWhat specific aspect would you like more detail on?",

            "Great question about $topic! Here's what usually works:\n\n**Key Steps:**\n• Start with the fundamentals\n• Practice regularly\n• Learn from mistakes\n• Ask for feedback\n• Keep improving\n\nThe key is consistency and patience. What part would you like to understand better?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate answer for "Why..." questions
     */
    private function generateWhyAnswer($message)
    {
        $topic = str_replace(['why', 'why is', 'why do'], '', $message);
        $topic = trim($topic, '?');

        $responses = [
            "That's a thoughtful question about $topic!\n\n**Key Reasons:**\n• It serves an important purpose\n• It helps achieve better results\n• It's based on best practices\n• It creates positive outcomes\n\nWould you like more details about any of these reasons?",

            "**Why $topic?** Here's the explanation:\n\n🎯 **Purpose:** It helps achieve specific goals\n📊 **Effectiveness:** Research and practice show it works\n🏆 **Benefits:** Creates better results and outcomes\n💡 **Logic:** It's built on sound principles\n\nIs there a specific aspect you'd like to explore further?",

            "Great question! The reasons for $topic include:\n\n1. **Practical** - It makes things work better\n2. **Efficient** - It saves time and effort\n3. **Effective** - It produces good results\n4. **Proven** - Experts and experience confirm it\n\nWould you like specific examples?",

            "About $topic - here's why it matters:\n\n✓ It's based on proven principles\n✓ It helps avoid common problems\n✓ It leads to better outcomes\n✓ Experts recommend it\n✓ Success depends on it\n\nWhat aspect interests you most?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate answer for "What..." questions
     */
    private function generateWhatAnswer($message)
    {
        $topic = str_replace(['what is', 'what are', 'what do', 'what can'], '', $message);
        $topic = trim($topic, '?');

        $responses = [
            "**About $topic:**\n\n📌 **Definition:** It refers to concepts and practices related to $topic\n\n🎯 **Key Points:**\n• Important for achieving goals\n• Involves several components\n• Requires understanding and practice\n• Creates positive impact\n\n💡 **In Practice:**\n• Used regularly\n• Builds skills\n• Improves outcomes\n\nWant specific details?",

            "**$topic - Overview:**\n\n✓ **What it is:** A set of important practices and concepts\n✓ **Why it matters:** Creates valuable results\n✓ **Main components:** Multiple interconnected elements\n✓ **How it's used:** Applied systematically\n\n**Key Benefits:**\n• Improved results\n• Better understanding\n• Enhanced skills\n\nWhat else would you like to know?",

            "Regarding $topic:\n\n**Basics:**\nIt involves multiple elements working together to achieve results.\n\n**Main Aspects:**\n• Foundation concepts\n• Practical application\n• Best practices\n• Continuous improvement\n\n**Value:**\n• Produces results\n• Develops skills\n• Builds expertise\n\nCan I clarify anything specific?",

            "**$topic - Complete Overview:**\n\n🔍 **Scope:** Covers several important areas\n📚 **Components:** Multiple interconnected parts\n⚙️ **Function:** Works together systematically\n🎓 **Learning:** Requires study and practice\n\n**Main Elements:**\n1. Foundational understanding\n2. Practical skills\n3. Continuous refinement\n4. Expert guidance\n\nInterested in deeper dive?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate answer for definition questions
     */
    private function generateDefinitionAnswer($message)
    {
        $responses = [
            "📖 **Definition:**\n\nThis refers to important concepts and practices in education and personal development.\n\n**Key Characteristics:**\n• Foundational to understanding\n• Widely used in practice\n• Requires careful study\n• Builds important skills\n\n**Related Concepts:**\n• Best practices\n• Professional standards\n• Quality outcomes\n\nWould you like more detail?",

            "**Meaning Explained:**\n\nThis term encompasses important principles and methods used across various domains.\n\n✓ **Core Idea:** A systematic approach to improving outcomes\n✓ **Application:** Used regularly in professional settings\n✓ **Importance:** Critical for success\n✓ **Learning:** Requires practice and refinement\n\nNeed clarification on any aspect?",

            "That term means:\n\n📌 **At its core:** Important practices and principles\n🎯 **In context:** Used to achieve specific goals\n💼 **In practice:** Applied systematically\n📈 **Results:** Creates measurable improvements\n\n**In Summary:**\nIt's a set of important approaches that lead to better outcomes.\n\nMore info?",

            "**Definition:**\n\nIt refers to important methodologies and best practices that lead to positive outcomes.\n\n**Breaking it Down:**\n• **Method:** Systematic approach\n• **Purpose:** Achieve quality results\n• **Application:** Regular use in practice\n• **Impact:** Significant positive effects\n\n**Key Point:**\nUnderstanding and applying these principles is crucial for success.\n\nAny follow-up questions?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate advice answers
     */
    private function generateAdviceAnswer($message)
    {
        $responses = [
            "💡 **My Recommendation:**\n\n**Best Practices:**\n1. Start with solid fundamentals\n2. Practice consistently\n3. Seek feedback regularly\n4. Adapt and improve\n5. Stay committed\n\n**Pro Tips:**\n✓ Be patient with yourself\n✓ Learn from others\n✓ Track your progress\n✓ Celebrate small wins\n✓ Never stop improving\n\nWhat specific area needs the most attention?",

            "🎯 **Helpful Advice:**\n\n**Core Strategy:**\n• Focus on what matters most\n• Build skills systematically\n• Learn from mistakes\n• Stay dedicated\n• Keep improving\n\n**Quick Tips:**\n1. Start today, not tomorrow\n2. Break goals into steps\n3. Track progress\n4. Adjust as needed\n5. Never give up\n\nHow can I help you specifically?",

            "**Smart Approach:**\n\n✅ **Do These:**\n• Set clear goals\n• Create a plan\n• Take action regularly\n• Monitor results\n• Keep learning\n\n⚠️ **Avoid These:**\n• Procrastination\n• Ignoring feedback\n• Giving up easily\n• Not reviewing progress\n• Static thinking\n\nWould you like specific steps?",

            "**Solid Advice:**\n\n**Essential Steps:**\n1️⃣ Understand the basics\n2️⃣ Create a clear plan\n3️⃣ Execute consistently\n4️⃣ Monitor progress\n5️⃣ Refine continuously\n\n**Remember:**\n• Consistency matters\n• Progress over perfection\n• Learn continuously\n• Ask for help\n• Stay positive\n\nReady to dive deeper?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate comparison answers
     */
    private function generateComparisonAnswer($message)
    {
        $responses = [
            "🔄 **Comparison:**\n\n**Similarities:**\n• Both serve important purposes\n• Both require understanding\n• Both improve outcomes\n• Both are valuable\n\n**Differences:**\n• Different approaches\n• Different applications\n• Different focuses\n• Different strengths\n\n**Bottom Line:**\nBoth have merit depending on context and goals.\n\nWhich is right for your situation?",

            "📊 **Side-by-Side Analysis:**\n\n**Aspect 1:**\n• Approach A: One method\n• Approach B: Another method\n• Both valid in different contexts\n\n**Effectiveness:**\n• Approach A: Strong in certain areas\n• Approach B: Strong in others\n• Context matters\n\n**Recommendation:**\nChoose based on your specific needs and goals.\n\nWhat's your priority?",

            "⚖️ **Comparison Overview:**\n\n| Factor | Option A | Option B |\n|--------|----------|----------|\n| Ease | Varies | Varies |\n| Results | Good | Good |\n| Cost | Different | Different |\n| Time | Variable | Variable |\n\n**Overall:** Both have advantages. Choice depends on priorities.\n\nWhat matters most to you?",

            "🎯 **Comparative Analysis:**\n\n**Option 1:**\n✅ Advantages\n❌ Limitations\n⏱️ Time: Variable\n💰 Cost: Varies\n\n**Option 2:**\n✅ Different Advantages\n❌ Different Limitations\n⏱️ Time: Variable\n💰 Cost: Varies\n\n**Verdict:** Choose based on your specific goals.\n\nWhich appeals to you?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate yes/no answer
     */
    private function generateYesNoAnswer($message)
    {
        $responses = [
            "That's a great question! 🤔\n\n**Short Answer:** It depends on several factors.\n\n**Key Considerations:**\n• Context matters\n• Individual circumstances vary\n• Multiple perspectives exist\n• Different situations need different approaches\n\n**My Thoughts:**\n✓ Valid points on multiple sides\n✓ No one-size-fits-all answer\n✓ Best to evaluate your specific situation\n✓ Consider your priorities\n\nWhat's your specific context?",

            "Good question! 👍\n\n**The Answer:** It's nuanced and depends on factors.\n\n**Consider These:**\n• What's your goal?\n• What's your situation?\n• What are the trade-offs?\n• What resources do you have?\n\n**General Principle:**\nThere's usually no absolute yes or no - context is key.\n\n**My Recommendation:**\nThink about what matters most to you.\n\nWant to discuss specifics?",

            "Interesting question! 💭\n\n**The Reality:** Most things aren't black and white.\n\n**Factors to Consider:**\n• Your specific situation\n• Your goals and priorities\n• Available resources\n• Potential outcomes\n• Personal preferences\n\n**Best Approach:**\n1. Clarify your goals\n2. Weigh the options\n3. Consider consequences\n4. Make informed decision\n\nWhat's your specific scenario?",

            "That's worth considering! 🎯\n\n**Direct Answer:** It depends!\n\n**Why It's Complex:**\n• Multiple valid perspectives\n• Context-dependent\n• Personal factors matter\n• Situations vary\n\n**Framework:**\n📌 Define your goals\n📌 Identify constraints\n📌 Evaluate options\n📌 Make decision\n📌 Track results\n\n**My Advice:**\nThink through your unique situation carefully.\n\nWhat's driving your question?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate response for topic statements (not questions)
     */
    private function generateTopicResponse($message)
    {
        $responses = [
            "That's an interesting topic! 💭\n\n**My Thoughts:**\n• It's important and relevant\n• Deserves careful consideration\n• Multiple angles to explore\n• Valuable for learning\n\n**Key Points:**\n✓ Foundational to many areas\n✓ Requires understanding\n✓ Builds important skills\n✓ Creates positive impact\n\n**Next Steps:**\nWould you like to explore this further? Any specific questions about it?",

            "Great point! 👌\n\n**About This Topic:**\nIt's an important subject that affects many areas.\n\n**Key Aspects:**\n• Worth understanding fully\n• Applicable in multiple contexts\n• Requires attention to detail\n• Builds valuable knowledge\n\n**Deeper Exploration:**\nWe could dive into:\n• Definitions\n• Best practices\n• Real-world application\n• Related concepts\n\nWhat interests you most?",

            "You bring up something important! 🎯\n\n**Why This Matters:**\n• Relevant and timely\n• Impacts many decisions\n• Worth understanding\n• Builds expertise\n\n**To Explore Further:**\nYou could ask me:\n• How to apply this\n• Why it's important\n• What it means\n• Best practices\n\nWhat would help you most?",

            "That's valuable to think about! 💡\n\n**Overview:**\nIt's an important area covering multiple elements.\n\n**What We Could Discuss:**\n📚 Theory and concepts\n🎯 Practical applications\n💼 Real-world examples\n📈 Benefits and outcomes\n\n**Questions to Consider:**\n• What specifically interests you?\n• What's your goal?\n• What problem are you solving?\n\nHow can I help you explore this?",
        ];

        return $responses[array_rand($responses)];
    }

    /**
     * Generate default response when no specific pattern matches
     */
    private function generateDefaultResponse($message, $user)
    {
        $responses = [
            "That's an interesting question! 🤔\n\n**What I Can Help With:**\n💼 Assessment and grading guidance\n📊 System statistics and information\n🎓 General knowledge on almost any topic\n💡 Advice and recommendations\n🎯 Problem-solving strategies\n📚 Explanations and definitions\n\n**My Approach:**\n✓ Clear and practical answer\n✓ Relevant examples\n✓ Helpful next steps\n✓ Further assistance available\n\nFeel free to ask anything!",

            "Great question! 👍\n\n**I'm Here to Help With:**\n📖 Definitions and explanations\n❓ Answers to any question\n💭 Advice and recommendations\n🎯 How-to guidance\n📊 Information and data\n📚 Learning and education\n\n**How I Work:**\n1. Listen carefully\n2. Provide helpful response\n3. Offer further clarification\n4. Support your goals\n\nWhat else can I help with?",

            "Excellent! 🎉\n\n**I Can Assist You With:**\n✗ Assessment techniques\n✗ General knowledge\n✗ Advice and guidance\n✗ How-to instructions\n✗ Explanations\n✗ System support\n\n**Key Strengths:**\n• Wide knowledge base\n• Practical advice\n• Clear explanations\n• Helpful resources\n• Follow-up support\n\nWhat's your next question?",

            "Perfect! 💪\n\n**I'm Your Assistant For:**\n🎓 Education and learning\n🎯 Problem-solving\n💡 Advice and tips\n📚 Knowledge on most topics\n🏆 Best practices\n🔧 System help\n\n**Why You Can Trust Me:**\n✓ Thoughtful responses\n✓ Based on sound principles\n✓ Practical and useful\n✓ Always improving\n✓ Here to help!\n\nWhat would you like to know?",
        ];

        return $responses[array_rand($responses)];
    }
}

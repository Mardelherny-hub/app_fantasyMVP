<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seedea todas las configuraciones iniciales del sistema.
     */
    public function run(): void
    {
        $settings = $this->getSettings();

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ Settings seeded successfully!');
    }

    /**
     * Obtener todas las configuraciones del sistema.
     */
    private function getSettings(): array
    {
        return array_merge(
            $this->getGeneralSettings(),
            $this->getLeagueSettings(),
            $this->getMarketSettings(),
            $this->getQuizSettings(),
            $this->getScoringSettings(),
            $this->getRewardsSettings(),
            $this->getEmailSettings(),
            $this->getSecuritySettings(),
            $this->getSocialSettings()
        );
    }

    // ========================================
    // GENERAL SETTINGS
    // ========================================
    
    private function getGeneralSettings(): array
    {
        return [
            [
                'key' => 'app_name',
                'value' => 'EduCan Soccer',
                'group' => Setting::GROUP_GENERAL,
                'type' => Setting::TYPE_STRING,
                'label' => 'Application Name',
                'description' => 'The name of the application displayed throughout the site.',
                'validation_rules' => 'required|string|max:100',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'app_tagline',
                'value' => 'La experiencia de fantasy soccer educativa de Canadá',
                'group' => Setting::GROUP_GENERAL,
                'type' => Setting::TYPE_STRING,
                'label' => 'Application Tagline',
                'description' => 'Short tagline or slogan for the app.',
                'validation_rules' => 'nullable|string|max:255',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'group' => Setting::GROUP_GENERAL,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Maintenance Mode',
                'description' => 'Put the application in maintenance mode.',
                'is_editable' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'default_locale',
                'value' => 'es',
                'group' => Setting::GROUP_GENERAL,
                'type' => Setting::TYPE_STRING,
                'label' => 'Default Language',
                'description' => 'Default language for new users.',
                'options' => ['es' => 'Español', 'en' => 'English', 'fr' => 'Français'],
                'validation_rules' => 'required|in:es,en,fr',
                'is_editable' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'registration_enabled',
                'value' => '1',
                'group' => Setting::GROUP_GENERAL,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Registration',
                'description' => 'Allow new users to register.',
                'is_editable' => true,
                'sort_order' => 5,
            ],
        ];
    }

    // ========================================
    // LEAGUE SETTINGS
    // ========================================
    
    private function getLeagueSettings(): array
    {
        return [
            [
                'key' => 'max_leagues_per_user',
                'value' => '3',
                'group' => Setting::GROUP_LEAGUES,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Max Leagues per User',
                'description' => 'Maximum number of leagues a user can participate in simultaneously.',
                'validation_rules' => 'required|integer|min:1|max:10',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'require_league_approval',
                'value' => '1',
                'group' => Setting::GROUP_LEAGUES,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Require League Approval',
                'description' => 'Private leagues created by users require admin approval before being active.',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'allow_public_leagues',
                'value' => '1',
                'group' => Setting::GROUP_LEAGUES,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Allow Public Leagues',
                'description' => 'Allow creation of public leagues that anyone can join.',
                'is_editable' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'default_league_size',
                'value' => '10',
                'group' => Setting::GROUP_LEAGUES,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Default League Size',
                'description' => 'Default number of participants in a league.',
                'validation_rules' => 'required|integer|min:4|max:20',
                'is_editable' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'auto_fill_bots',
                'value' => '1',
                'group' => Setting::GROUP_LEAGUES,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Auto-fill with Bots',
                'description' => 'Automatically fill empty spots with bot teams.',
                'is_editable' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'league_code_length',
                'value' => '6',
                'group' => Setting::GROUP_LEAGUES,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'League Code Length',
                'description' => 'Length of the invitation code for private leagues.',
                'validation_rules' => 'required|integer|min:4|max:10',
                'is_editable' => false,
                'sort_order' => 6,
            ],
        ];
    }

    // ========================================
    // MARKET SETTINGS
    // ========================================
    
    private function getMarketSettings(): array
    {
        return [
            [
                'key' => 'market_enabled',
                'value' => '1',
                'group' => Setting::GROUP_MARKET,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Transfer Market',
                'description' => 'Enable the player transfer market.',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'initial_budget',
                'value' => '100.00',
                'group' => Setting::GROUP_MARKET,
                'type' => Setting::TYPE_FLOAT,
                'label' => 'Initial Budget',
                'description' => 'Starting budget for new fantasy teams.',
                'validation_rules' => 'required|numeric|min:50|max:500',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'max_transfers_per_gameweek',
                'value' => '3',
                'group' => Setting::GROUP_MARKET,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Max Transfers per Gameweek',
                'description' => 'Maximum number of player transfers allowed per gameweek.',
                'validation_rules' => 'required|integer|min:1|max:10',
                'is_editable' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'transfer_deadline_hours',
                'value' => '2',
                'group' => Setting::GROUP_MARKET,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Transfer Deadline (hours)',
                'description' => 'Hours before gameweek starts when transfers are locked.',
                'validation_rules' => 'required|integer|min:1|max:48',
                'is_editable' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'max_price_multiplier',
                'value' => '3.00',
                'group' => Setting::GROUP_MARKET,
                'type' => Setting::TYPE_FLOAT,
                'label' => 'Max Price Multiplier',
                'description' => 'Maximum multiplier for player market value (e.g., 3x means 300% of base value).',
                'validation_rules' => 'required|numeric|min:1|max:10',
                'is_editable' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'loans_enabled',
                'value' => '1',
                'group' => Setting::GROUP_MARKET,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Player Loans',
                'description' => 'Allow temporary player loans between teams.',
                'is_editable' => true,
                'sort_order' => 6,
            ],
        ];
    }

    // ========================================
    // QUIZ SETTINGS
    // ========================================
    
    private function getQuizSettings(): array
    {
        return [
            [
                'key' => 'quiz_enabled',
                'value' => '1',
                'group' => Setting::GROUP_QUIZ,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Quiz System',
                'description' => 'Enable the educational trivia/quiz system.',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'quiz_time_limit',
                'value' => '30',
                'group' => Setting::GROUP_QUIZ,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Time Limit per Question (seconds)',
                'description' => 'Time limit in seconds for each quiz question.',
                'validation_rules' => 'required|integer|min:10|max:300',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'quiz_questions_per_round',
                'value' => '10',
                'group' => Setting::GROUP_QUIZ,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Questions per Quiz Round',
                'description' => 'Number of questions in each quiz round.',
                'validation_rules' => 'required|integer|min:5|max:50',
                'is_editable' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'quiz_points_per_correct',
                'value' => '10',
                'group' => Setting::GROUP_QUIZ,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Points per Correct Answer',
                'description' => 'Points awarded for each correct quiz answer.',
                'validation_rules' => 'required|integer|min:1|max:100',
                'is_editable' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'quiz_daily_limit',
                'value' => '5',
                'group' => Setting::GROUP_QUIZ,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Daily Quiz Limit',
                'description' => 'Maximum quiz attempts per day per user (0 = unlimited).',
                'validation_rules' => 'required|integer|min:0|max:50',
                'is_editable' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'quiz_pvp_enabled',
                'value' => '1',
                'group' => Setting::GROUP_QUIZ,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable PvP Challenges',
                'description' => 'Allow users to challenge each other in quiz battles.',
                'is_editable' => true,
                'sort_order' => 6,
            ],
        ];
    }

    // ========================================
    // SCORING SETTINGS
    // ========================================
    
    private function getScoringSettings(): array
    {
        return [
            [
                'key' => 'points_goal_fw',
                'value' => '4',
                'group' => Setting::GROUP_SCORING,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Points - Goal (Forward)',
                'description' => 'Points awarded when a forward scores a goal.',
                'validation_rules' => 'required|integer',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'points_goal_mf',
                'value' => '5',
                'group' => Setting::GROUP_SCORING,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Points - Goal (Midfielder)',
                'description' => 'Points awarded when a midfielder scores a goal.',
                'validation_rules' => 'required|integer',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'points_goal_df',
                'value' => '6',
                'group' => Setting::GROUP_SCORING,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Points - Goal (Defender)',
                'description' => 'Points awarded when a defender scores a goal.',
                'validation_rules' => 'required|integer',
                'is_editable' => true,
                'sort_order' => 3,
            ],
            [
                'key' => 'points_assist',
                'value' => '3',
                'group' => Setting::GROUP_SCORING,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Points - Assist',
                'description' => 'Points awarded for an assist.',
                'validation_rules' => 'required|integer',
                'is_editable' => true,
                'sort_order' => 4,
            ],
            [
                'key' => 'points_clean_sheet_gk',
                'value' => '4',
                'group' => Setting::GROUP_SCORING,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Points - Clean Sheet (Goalkeeper)',
                'description' => 'Points awarded to goalkeeper for clean sheet.',
                'validation_rules' => 'required|integer',
                'is_editable' => true,
                'sort_order' => 5,
            ],
            [
                'key' => 'captain_multiplier',
                'value' => '2',
                'group' => Setting::GROUP_SCORING,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Captain Points Multiplier',
                'description' => 'Multiplier for captain points (2 = double points).',
                'validation_rules' => 'required|integer|min:2|max:3',
                'is_editable' => true,
                'sort_order' => 10,
            ],
        ];
    }

    // ========================================
    // REWARDS SETTINGS
    // ========================================
    
    private function getRewardsSettings(): array
    {
        return [
            [
                'key' => 'rewards_enabled',
                'value' => '1',
                'group' => Setting::GROUP_REWARDS,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Rewards System',
                'description' => 'Enable coins, badges, and rewards.',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'daily_login_reward',
                'value' => '10',
                'group' => Setting::GROUP_REWARDS,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Daily Login Reward (coins)',
                'description' => 'Coins awarded for daily login.',
                'validation_rules' => 'required|integer|min:0',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'quiz_completion_reward',
                'value' => '50',
                'group' => Setting::GROUP_REWARDS,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Quiz Completion Reward (coins)',
                'description' => 'Coins awarded for completing a quiz.',
                'validation_rules' => 'required|integer|min:0',
                'is_editable' => true,
                'sort_order' => 3,
            ],
        ];
    }

    // ========================================
    // EMAIL SETTINGS
    // ========================================
    
    private function getEmailSettings(): array
    {
        return [
            [
                'key' => 'email_notifications_enabled',
                'value' => '1',
                'group' => Setting::GROUP_EMAIL,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Email Notifications',
                'description' => 'Enable system email notifications.',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'notify_league_invitation',
                'value' => '1',
                'group' => Setting::GROUP_EMAIL,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Notify - League Invitation',
                'description' => 'Send email when user is invited to a league.',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'notify_gameweek_deadline',
                'value' => '1',
                'group' => Setting::GROUP_EMAIL,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Notify - Gameweek Deadline',
                'description' => 'Send reminder before gameweek deadline.',
                'is_editable' => true,
                'sort_order' => 3,
            ],
        ];
    }

    // ========================================
    // SECURITY SETTINGS
    // ========================================
    
    private function getSecuritySettings(): array
    {
        return [
            [
                'key' => 'require_email_verification',
                'value' => '1',
                'group' => Setting::GROUP_SECURITY,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Require Email Verification',
                'description' => 'Users must verify email before accessing the app.',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'min_password_length',
                'value' => '8',
                'group' => Setting::GROUP_SECURITY,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Minimum Password Length',
                'description' => 'Minimum characters required for passwords.',
                'validation_rules' => 'required|integer|min:6|max:32',
                'is_editable' => true,
                'sort_order' => 2,
            ],
            [
                'key' => 'session_lifetime',
                'value' => '120',
                'group' => Setting::GROUP_SECURITY,
                'type' => Setting::TYPE_INTEGER,
                'label' => 'Session Lifetime (minutes)',
                'description' => 'How long user sessions remain active.',
                'validation_rules' => 'required|integer|min:30|max:1440',
                'is_editable' => true,
                'sort_order' => 3,
            ],
        ];
    }

    // ========================================
    // SOCIAL SETTINGS
    // ========================================
    
    private function getSocialSettings(): array
    {
        return [
            [
                'key' => 'social_login_google',
                'value' => '1',
                'group' => Setting::GROUP_SOCIAL,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Google Login',
                'description' => 'Allow users to login with Google.',
                'is_editable' => true,
                'sort_order' => 1,
            ],
            [
                'key' => 'social_login_facebook',
                'value' => '1',
                'group' => Setting::GROUP_SOCIAL,
                'type' => Setting::TYPE_BOOLEAN,
                'label' => 'Enable Facebook Login',
                'description' => 'Allow users to login with Facebook.',
                'is_editable' => true,
                'sort_order' => 2,
            ],
        ];
    }
}
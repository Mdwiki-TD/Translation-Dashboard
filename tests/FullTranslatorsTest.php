<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class FullTranslatorsTest extends TestCase
{
    public function testFullTranslatorActiveCheck(): void
    {
        $full_translators = [
            ['id' => 1, 'user' => 'UserActive', 'is_active' => 1],
            ['id' => 2, 'user' => 'UserInactive', 'is_active' => 0],
        ];

        $translators_map = array_column($full_translators, 'is_active', 'user');

        $user_active_full_tr = ($translators_map['UserActive'] ?? 0) == 1;
        $user_inactive_full_tr = ($translators_map['UserInactive'] ?? 0) == 1;
        $user_missing_full_tr = ($translators_map['UserMissing'] ?? 0) == 1;

        $this->assertTrue($user_active_full_tr, 'User with is_active == 1 should be recognized as full translator.');
        $this->assertFalse($user_inactive_full_tr, 'User with is_active == 0 should not be recognized as full translator.');
        $this->assertFalse($user_missing_full_tr, 'User not in list should not be recognized as full translator.');
    }

    public function testResultsTableFullTranslatorFilter(): void
    {
        $items = [
            'Test Article' => [
                'title' => 'Test Article',
                'category' => 'RTT',
                'importance' => 'High',
                'r_lead_refs' => 2,
                'r_all_refs' => 5,
                'en_views' => 100,
                'w_lead_words' => 50,
                'w_all_words' => 200,
                'qid' => 'Q123',
                'target' => ''
            ]
        ];

        // For non-full translator user
        $html_non_full = \Results\GetResults2026\make_results_table_2026(
            $items,
            'ar',
            'RTT',
            'Main',
            'lead',
            false,
            'RegularUser',
            ['Test Article'], // nolead_translates (article lead not allowed alone if full only)
            ['Test Article']  // translates_full
        );

        // Full link/button should not appear for RegularUser
        $this->assertStringNotContainsString("Full</a>", $html_non_full);

        // For active full translator user
        $html_full = \Results\GetResults2026\make_results_table_2026(
            $items,
            'ar',
            'RTT',
            'Main',
            'lead',
            true,
            'ActiveFullTranslatorUser',
            ['Test Article'],
            ['Test Article']
        );

        // Full link/button SHOULD appear for ActiveFullTranslatorUser
        $this->assertStringContainsString("Full</a>", $html_full);
    }
}

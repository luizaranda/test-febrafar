<?php

namespace Tests\Unit\Models;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_relation()
    {
        $user = User::factory()->create();
        $activity = Activity::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $activity->user);
        $this->assertEquals($user->id, $activity->user->id);
    }

    public function test_filter_by_date_range_scope()
    {
        $startDate = now()->subDays(5);
        $dueDate = now()->addDays(5);

        $activity1 = Activity::factory()->create(['start_date' => $startDate, 'due_date' => $dueDate]);
        $activity2 = Activity::factory()->create(['start_date' => $startDate->addDays(1), 'due_date' => $dueDate->subDays(5)]);
        $activity3 = Activity::factory()->create(['start_date' => $startDate->subDays(10), 'due_date' => $dueDate->subDays(10)]);

        $filteredActivities = Activity::filterByDateRange(now()->subDays(5), now()->addDays(5))->get();

        $this->assertCount(2, $filteredActivities);
        $this->assertTrue($filteredActivities->contains($activity1));
        $this->assertTrue($filteredActivities->contains($activity2));
        $this->assertFalse($filteredActivities->contains($activity3));
    }
}

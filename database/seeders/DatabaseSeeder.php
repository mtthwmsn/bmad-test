<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\Competitor;
use App\Models\Stage;
use App\Models\Measure;
use App\Models\Attempt;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('Seeding database with Pour Test data...');

        // Create 5 competitors
        $this->command->info('Creating 5 competitors...');
        $competitors = Competitor::factory()->count(5)->create();

        // Create draft competition
        $this->command->info('Creating draft competition...');
        $draftCompetition = Competition::factory()->draft()->create([
            'name' => 'London Pour Test 2025',
            'city' => 'London',
            'country_code' => 'GB',
        ]);

        // Create 3 stages for draft competition
        foreach (range(1, 3) as $order) {
            $stage = Stage::factory()->pending()->create([
                'competition_id' => $draftCompetition->id,
                'order' => $order,
            ]);

            // Create 2-4 measures per stage
            $measureCount = rand(2, 4);
            foreach (range(1, $measureCount) as $measureOrder) {
                Measure::factory()->create([
                    'stage_id' => $stage->id,
                    'order' => $measureOrder,
                ]);
            }
        }

        // Create active competition
        $this->command->info('Creating active competition...');
        $activeCompetition = Competition::factory()->active()->create([
            'name' => 'NYC Bartender Challenge 2025',
            'city' => 'New York',
            'country_code' => 'US',
        ]);

        // Create 3 stages for active competition
        $stages = collect();
        foreach (range(1, 3) as $order) {
            $stage = Stage::factory()->pending()->create([
                'competition_id' => $activeCompetition->id,
                'order' => $order,
            ]);
            $stages->push($stage);

            // Create 2-4 measures per stage
            $measureCount = rand(2, 4);
            foreach (range(1, $measureCount) as $measureOrder) {
                Measure::factory()->create([
                    'stage_id' => $stage->id,
                    'order' => $measureOrder,
                ]);
            }
        }

        // Attach all competitors to active competition
        $this->command->info('Attaching competitors to active competition...');
        $activeCompetition->competitors()->attach($competitors->pluck('id'));

        // Create attempts with results for active competition
        $this->command->info('Creating attempts with results...');

        // Have each competitor complete the first stage
        $firstStage = $stages->first();
        $firstStage->update(['status' => 'complete']);

        foreach ($competitors as $competitor) {
            // Create attempt for first stage
            $attempt = Attempt::factory()->create([
                'competition_id' => $activeCompetition->id,
                'competitor_id' => $competitor->id,
                'stage_id' => $firstStage->id,
            ]);

            // Create results for each measure in this stage
            foreach ($firstStage->measures as $measure) {
                // Generate pour close to target with some variance
                $variance = rand(-5, 5);
                $pouredMl = $measure->target_ml + $variance;

                $attempt->results()->create([
                    'measure_id' => $measure->id,
                    'poured_ml' => max(0, $pouredMl),
                ]);
            }
        }

        // Have 3 competitors complete the second stage
        $secondStage = $stages->get(1);
        $secondStage->update(['status' => 'active']);

        foreach ($competitors->take(3) as $competitor) {
            $attempt = Attempt::factory()->create([
                'competition_id' => $activeCompetition->id,
                'competitor_id' => $competitor->id,
                'stage_id' => $secondStage->id,
            ]);

            foreach ($secondStage->measures as $measure) {
                $variance = rand(-5, 5);
                $pouredMl = $measure->target_ml + $variance;

                $attempt->results()->create([
                    'measure_id' => $measure->id,
                    'poured_ml' => max(0, $pouredMl),
                ]);
            }
        }

        $this->command->info('Database seeding completed!');
        $this->command->info('Created:');
        $this->command->info('  - 2 competitions (1 draft, 1 active)');
        $this->command->info('  - 6 stages (3 per competition)');
        $this->command->info('  - ' . Measure::count() . ' measures');
        $this->command->info('  - 5 competitors');
        $this->command->info('  - ' . Attempt::count() . ' attempts');
        $this->command->info('  - ' . \App\Models\AttemptResult::count() . ' results');
    }
}

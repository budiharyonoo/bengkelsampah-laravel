<?php

namespace App\Console\Commands;

use App\Models\Setoran;
use App\Models\WasteTracking;
use App\Services\Inventory\InventoryService;
use Illuminate\Console\Command;

class MigrateExistingSetoranToInventory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:migrate-existing
                            {--dry-run : Run without making changes}
                            {--force : Force re-migration of already migrated setorans}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing completed setorans to the inventory tracking system';

    /**
     * Execute the console command.
     */
    public function handle(InventoryService $inventoryService): int
    {
        $this->info('Starting migration of existing setorans to inventory...');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($isDryRun) {
            $this->warn('Running in dry-run mode - no changes will be made.');
            $this->newLine();
        }

        // Get all completed setorans
        $query = Setoran::where('status', 'selesai')
            ->whereNotNull('items_json')
            ->orderBy('tanggal_selesai');

        $totalSetorans = $query->count();

        if ($totalSetorans === 0) {
            $this->info('No completed setorans found to migrate.');

            return Command::SUCCESS;
        }

        $this->info("Found {$totalSetorans} completed setorans to process.");
        $this->newLine();

        $bar = $this->output->createProgressBar($totalSetorans);
        $bar->start();

        $migrated = 0;
        $skipped = 0;
        $errors = 0;

        $query->cursor()->each(function ($setoran) use (
            $inventoryService,
            $isDryRun,
            $force,
            &$migrated,
            &$skipped,
            &$errors,
            $bar
        ) {
            // Check if already migrated
            $alreadyMigrated = WasteTracking::where('setoran_id', $setoran->id)->exists();

            if ($alreadyMigrated && ! $force) {
                $skipped++;
                $bar->advance();

                return;
            }

            if ($alreadyMigrated && $force) {
                // Delete existing tracking records for this setoran
                if (! $isDryRun) {
                    WasteTracking::where('setoran_id', $setoran->id)->delete();
                }
            }

            try {
                if (! $isDryRun) {
                    $inventoryService->addFromSetoran($setoran);
                }
                $migrated++;
            } catch (\Exception $e) {
                $errors++;
                $this->newLine();
                $this->error("Error migrating setoran #{$setoran->id}: {$e->getMessage()}");
            }

            $bar->advance();
        });

        $bar->finish();
        $this->newLine(2);

        // Summary
        $this->info('Migration Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Setorans', $totalSetorans],
                ['Successfully Migrated', $migrated],
                ['Skipped (already migrated)', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->warn('This was a dry run. Run without --dry-run to apply changes.');
        }

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}

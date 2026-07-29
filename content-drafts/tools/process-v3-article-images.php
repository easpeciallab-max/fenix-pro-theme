<?php
/**
 * Crop approved AI source images into optimized article media.
 *
 * Contact-sheet quadrants:
 *   1 = top-left, 2 = top-right, 3 = bottom-left, 4 = bottom-right.
 *
 * Usage:
 *   php content-drafts/tools/process-v3-article-images.php --start=11 --limit=10
 */

declare(strict_types=1);

$options   = getopt('', array('start::', 'limit::'));
$startId   = max(1, (int) ($options['start'] ?? 11));
$limit     = max(1, (int) ($options['limit'] ?? 10));
$root      = dirname(__DIR__);
$sourceDir = $root . '/generated/media-v3/sources';
$outputDir = $root . '/generated/media-v3/article-images';
$coverDir  = $root . '/generated/media-v3/cover-backgrounds';

if (!extension_loaded('gd') || !function_exists('imagewebp')) {
    fwrite(STDERR, "PHP GD with WebP support is required.\n");
    exit(1);
}

foreach (array($outputDir, $coverDir) as $directory) {
    if (!is_dir($directory) && !mkdir($directory, 0777, true) && !is_dir($directory)) {
        fwrite(STDERR, "Unable to create output directory: {$directory}\n");
        exit(1);
    }
}

/**
 * The filenames are intentionally descriptive because WordPress keeps them in
 * attachment URLs. Never infer an article from file order.
 *
 * @var array<int, array<int, array{source: string, output: string, quadrant?: int}>>
 */
$jobs = array(
    11 => array(
        array(
            'source' => 'mt5-experts-journal-logs-overview.png',
            'output' => 'mt5-experts-journal-logs-overview.webp',
        ),
        array(
            'source' => 'mt5-experts-journal-logs-reading-log-columns.png',
            'output' => 'mt5-experts-journal-logs-reading-time-source-message.webp',
        ),
        array(
            'source' => 'mt5-experts-journal-logs-support-evidence.png',
            'output' => 'mt5-experts-journal-logs-support-evidence.webp',
        ),
    ),
    12 => array(
        array(
            'source' => 'magic-number-mt5-ea-overview.png',
            'output' => 'magic-number-mt5-ea-overview.webp',
        ),
        array(
            'source' => 'magic-number-mt5-ea-id-registry.png',
            'output' => 'magic-number-mt5-ea-id-registry.webp',
        ),
        array(
            'source' => 'magic-number-mt5-ea-order-isolation.png',
            'output' => 'magic-number-mt5-ea-order-isolation.webp',
        ),
    ),
    13 => array(
        array(
            'source' => 'mt5-ea-preset-set-file-overview.png',
            'output' => 'mt5-ea-preset-set-file-overview.webp',
        ),
        array(
            'source'   => 'mt5-ea-preset-set-file-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'mt5-ea-preset-set-file-version-match.webp',
        ),
        array(
            'source'   => 'mt5-ea-preset-set-file-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'mt5-ea-preset-set-file-library-backup.webp',
        ),
    ),
    14 => array(
        array(
            'source'   => 'update-ea-mt5-safely-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'update-ea-mt5-safely-backup-before-update.webp',
        ),
        array(
            'source'   => 'update-ea-mt5-safely-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'update-ea-mt5-safely-replace-version.webp',
        ),
        array(
            'source'   => 'update-ea-mt5-safely-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'update-ea-mt5-safely-validation-rollback.webp',
        ),
    ),
    15 => array(
        array(
            'source' => 'mt5-no-connection-invalid-account-overview.png',
            'output' => 'mt5-no-connection-invalid-account-overview.webp',
        ),
        array(
            'source'   => 'mt5-no-connection-invalid-account-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'mt5-no-connection-invalid-account-server-match.webp',
        ),
        array(
            'source'   => 'mt5-no-connection-invalid-account-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'mt5-no-connection-invalid-account-login-path.webp',
        ),
    ),
    16 => array(
        array(
            'source' => 'mt5-broker-server-time-ea-overview.png',
            'output' => 'mt5-broker-server-time-ea-overview.webp',
        ),
        array(
            'source'   => 'mt5-broker-server-time-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'mt5-broker-server-time-ea-market-sessions.webp',
        ),
        array(
            'source'   => 'mt5-broker-server-time-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'mt5-broker-server-time-ea-daylight-saving.webp',
        ),
    ),
    17 => array(
        array(
            'source' => 'multiple-mt5-terminals-accounts-overview.png',
            'output' => 'multiple-mt5-terminals-accounts-overview.webp',
        ),
        array(
            'source'   => 'multiple-mt5-terminals-accounts-contact-sheet.png',
            'quadrant' => 1,
            'output'   => 'multiple-mt5-terminals-accounts-operations.webp',
        ),
        array(
            'source'   => 'multiple-mt5-terminals-accounts-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'multiple-mt5-terminals-accounts-resource-monitoring.webp',
        ),
    ),
    18 => array(
        array(
            'source'   => 'how-to-backtest-ea-mt5-contact-sheet.png',
            'quadrant' => 1,
            'output'   => 'how-to-backtest-ea-mt5-strategy-tester-setup.webp',
        ),
        array(
            'source'   => 'how-to-backtest-ea-mt5-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'how-to-backtest-ea-mt5-historical-replay.webp',
        ),
        array(
            'source'   => 'how-to-backtest-ea-mt5-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'how-to-backtest-ea-mt5-report-analysis.webp',
        ),
    ),
    19 => array(
        array(
            'source' => 'mt5-real-ticks-backtest-overview.png',
            'output' => 'mt5-real-ticks-backtest-overview.webp',
        ),
        array(
            'source'   => 'mt5-real-ticks-backtest-contact-sheet.png',
            'quadrant' => 1,
            'output'   => 'mt5-real-ticks-backtest-real-vs-generated.webp',
        ),
        array(
            'source'   => 'mt5-real-ticks-backtest-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'mt5-real-ticks-backtest-bid-ask.webp',
        ),
    ),
    20 => array(
        array(
            'source' => 'in-sample-out-of-sample-ea-overview.png',
            'output' => 'in-sample-out-of-sample-ea-overview.webp',
        ),
        array(
            'source'   => 'in-sample-out-of-sample-ea-contact-sheet.png',
            'quadrant' => 1,
            'output'   => 'in-sample-out-of-sample-ea-data-split.webp',
        ),
        array(
            'source'   => 'in-sample-out-of-sample-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'in-sample-out-of-sample-ea-overfitting-validation.webp',
        ),
    ),
    21 => array(
        array(
            'source'   => 'walk-forward-analysis-ea-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'walk-forward-analysis-ea-cover-bg.png',
        ),
        array(
            'source'   => 'walk-forward-analysis-ea-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'walk-forward-analysis-ea-windows-overview.webp',
        ),
        array(
            'source'   => 'walk-forward-analysis-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'walk-forward-analysis-ea-rolling-vs-anchored.webp',
        ),
        array(
            'source'   => 'walk-forward-analysis-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'walk-forward-analysis-ea-stitched-results.webp',
        ),
    ),
    22 => array(
        array(
            'source'   => 'monte-carlo-testing-ea-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'monte-carlo-testing-ea-cover-bg.png',
        ),
        array(
            'source'   => 'monte-carlo-testing-ea-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'monte-carlo-testing-ea-shuffle-trade-order.webp',
        ),
        array(
            'source'   => 'monte-carlo-testing-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'monte-carlo-testing-ea-outcome-distribution.webp',
        ),
        array(
            'source'   => 'monte-carlo-testing-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'monte-carlo-testing-ea-tail-risk-review.webp',
        ),
    ),
    23 => array(
        array(
            'source'   => 'backtest-spread-commission-slippage-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'backtest-spread-commission-slippage-cover-bg.png',
        ),
        array(
            'source'   => 'backtest-spread-commission-slippage-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'backtest-spread-commission-slippage-spread-commission.webp',
        ),
        array(
            'source'   => 'backtest-spread-commission-slippage-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'backtest-spread-commission-slippage-cost-scenarios.webp',
        ),
        array(
            'source'   => 'backtest-spread-commission-slippage-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'backtest-spread-commission-slippage-execution-delay.webp',
        ),
    ),
    24 => array(
        array(
            'source'   => 'how-long-to-backtest-ea-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'how-long-to-backtest-ea-cover-bg.png',
        ),
        array(
            'source'   => 'how-long-to-backtest-ea-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'how-long-to-backtest-ea-market-regimes.webp',
        ),
        array(
            'source'   => 'how-long-to-backtest-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'how-long-to-backtest-ea-sample-window.webp',
        ),
        array(
            'source'   => 'how-long-to-backtest-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'how-long-to-backtest-ea-data-length-trade-count.webp',
        ),
    ),
    25 => array(
        array(
            'source'   => 'compare-backtest-forward-test-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'compare-backtest-forward-test-cover-bg.png',
        ),
        array(
            'source'   => 'compare-backtest-forward-test-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'compare-backtest-forward-test-reconciliation.webp',
        ),
        array(
            'source'   => 'compare-backtest-forward-test-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'compare-backtest-forward-test-execution-differences.webp',
        ),
        array(
            'source'   => 'compare-backtest-forward-test-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'compare-backtest-forward-test-lab-vs-live.webp',
        ),
    ),
    26 => array(
        array(
            'source'   => 'read-equity-curve-ea-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'read-equity-curve-ea-cover-bg.png',
        ),
        array(
            'source'   => 'read-equity-curve-ea-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'read-equity-curve-ea-balance-vs-equity.webp',
        ),
        array(
            'source'   => 'read-equity-curve-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'read-equity-curve-ea-drawdown-analysis.webp',
        ),
        array(
            'source'   => 'read-equity-curve-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'read-equity-curve-ea-curve-patterns.webp',
        ),
    ),
    27 => array(
        array(
            'source'   => 'risk-of-ruin-ea-trading-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'risk-of-ruin-ea-trading-cover-bg.png',
        ),
        array(
            'source'   => 'risk-of-ruin-ea-trading-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'risk-of-ruin-ea-trading-sequence-risk.webp',
        ),
        array(
            'source'   => 'risk-of-ruin-ea-trading-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'risk-of-ruin-ea-trading-outcome-distribution.webp',
        ),
        array(
            'source'   => 'risk-of-ruin-ea-trading-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'risk-of-ruin-ea-trading-risk-controls.webp',
        ),
    ),
    28 => array(
        array(
            'source'   => 'leverage-margin-level-ea-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'leverage-margin-level-ea-cover-bg.png',
        ),
        array(
            'source'   => 'leverage-margin-level-ea-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'leverage-margin-level-ea-margin-capacity.webp',
        ),
        array(
            'source'   => 'leverage-margin-level-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'leverage-margin-level-ea-contract-margin-review.webp',
        ),
        array(
            'source'   => 'leverage-margin-level-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'leverage-margin-level-ea-margin-buffer.webp',
        ),
    ),
    29 => array(
        array(
            'source'   => 'equity-stop-ea-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'equity-stop-ea-cover-bg.png',
        ),
        array(
            'source'   => 'equity-stop-ea-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'equity-stop-ea-emergency-cutoff.webp',
        ),
        array(
            'source'   => 'equity-stop-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'equity-stop-ea-layered-protection.webp',
        ),
        array(
            'source'   => 'equity-stop-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'equity-stop-ea-trigger-testing.webp',
        ),
    ),
    30 => array(
        array(
            'source'   => 'daily-loss-limit-ea-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'daily-loss-limit-ea-cover-bg.png',
        ),
        array(
            'source'   => 'daily-loss-limit-ea-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'daily-loss-limit-ea-server-day-cycle.webp',
        ),
        array(
            'source'   => 'daily-loss-limit-ea-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'daily-loss-limit-ea-shutdown-sequence.webp',
        ),
        array(
            'source'   => 'daily-loss-limit-ea-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'daily-loss-limit-ea-limit-review.webp',
        ),
    ),
    31 => array(
        array(
            'source'   => 'portfolio-exposure-multiple-orders-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'portfolio-exposure-multiple-orders-cover-bg.png',
        ),
        array(
            'source'   => 'portfolio-exposure-multiple-orders-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'portfolio-exposure-multiple-orders-currency-exposure.webp',
        ),
        array(
            'source'   => 'portfolio-exposure-multiple-orders-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'portfolio-exposure-multiple-orders-open-and-pending-orders.webp',
        ),
        array(
            'source'   => 'portfolio-exposure-multiple-orders-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'portfolio-exposure-multiple-orders-exposure-network.webp',
        ),
    ),
    32 => array(
        array(
            'source'   => 'correlation-risk-ea-portfolio-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'correlation-risk-ea-portfolio-cover-bg.png',
        ),
        array(
            'source'   => 'correlation-risk-ea-portfolio-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'correlation-risk-ea-portfolio-shared-market-current.webp',
        ),
        array(
            'source'   => 'correlation-risk-ea-portfolio-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'correlation-risk-ea-portfolio-correlation-clustering.webp',
        ),
        array(
            'source'   => 'correlation-risk-ea-portfolio-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'correlation-risk-ea-portfolio-stress-test.webp',
        ),
    ),
    33 => array(
        array(
            'source'   => 'fixed-lot-vs-auto-lot-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'fixed-lot-vs-auto-lot-cover-bg.png',
        ),
        array(
            'source'   => 'fixed-lot-vs-auto-lot-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'fixed-lot-vs-auto-lot-fixed-vs-adaptive-sizing.webp',
        ),
        array(
            'source'   => 'fixed-lot-vs-auto-lot-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'fixed-lot-vs-auto-lot-risk-based-lot-review.webp',
        ),
        array(
            'source'   => 'fixed-lot-vs-auto-lot-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'fixed-lot-vs-auto-lot-position-sizing-concept.webp',
        ),
    ),
    34 => array(
        array(
            'source'   => 'stop-loss-take-profit-trailing-stop-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'stop-loss-take-profit-trailing-stop-cover-bg.png',
        ),
        array(
            'source'   => 'stop-loss-take-profit-trailing-stop-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'stop-loss-take-profit-trailing-stop-exit-mechanisms.webp',
        ),
        array(
            'source'   => 'stop-loss-take-profit-trailing-stop-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'stop-loss-take-profit-trailing-stop-exit-settings-review.webp',
        ),
        array(
            'source'   => 'stop-loss-take-profit-trailing-stop-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'stop-loss-take-profit-trailing-stop-trailing-protection.webp',
        ),
    ),
    35 => array(
        array(
            'source'   => 'reduce-risk-after-drawdown-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'reduce-risk-after-drawdown-cover-bg.png',
        ),
        array(
            'source'   => 'reduce-risk-after-drawdown-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'reduce-risk-after-drawdown-diagnose-drawdown.webp',
        ),
        array(
            'source'   => 'reduce-risk-after-drawdown-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'reduce-risk-after-drawdown-drawdown-causes.webp',
        ),
        array(
            'source'   => 'reduce-risk-after-drawdown-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'reduce-risk-after-drawdown-safe-reentry.webp',
        ),
    ),
    36 => array(
        array(
            'source'   => 'choose-vps-specs-for-mt5-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'choose-vps-specs-for-mt5-cover-bg.png',
        ),
        array(
            'source'   => 'choose-vps-specs-for-mt5-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'choose-vps-specs-for-mt5-workload-measurement.webp',
        ),
        array(
            'source'   => 'choose-vps-specs-for-mt5-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'choose-vps-specs-for-mt5-cpu-ram-ssd-network.webp',
        ),
        array(
            'source'   => 'choose-vps-specs-for-mt5-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'choose-vps-specs-for-mt5-server-location-network.webp',
        ),
    ),
    37 => array(
        array(
            'source'   => 'mt5-vps-latency-ping-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'mt5-vps-latency-ping-cover-bg.png',
        ),
        array(
            'source'   => 'mt5-vps-latency-ping-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'mt5-vps-latency-ping-network-route-jitter.webp',
        ),
        array(
            'source'   => 'mt5-vps-latency-ping-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'mt5-vps-latency-ping-vps-comparison-test.webp',
        ),
        array(
            'source'   => 'mt5-vps-latency-ping-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'mt5-vps-latency-ping-latency-route-congestion.webp',
        ),
    ),
    38 => array(
        array(
            'source'   => 'run-multiple-mt5-on-vps-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'run-multiple-mt5-on-vps-cover-bg.png',
        ),
        array(
            'source'   => 'run-multiple-mt5-on-vps-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'run-multiple-mt5-on-vps-terminal-inventory.webp',
        ),
        array(
            'source'   => 'run-multiple-mt5-on-vps-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'run-multiple-mt5-on-vps-isolated-terminals-resources.webp',
        ),
        array(
            'source'   => 'run-multiple-mt5-on-vps-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'run-multiple-mt5-on-vps-staggered-start-monitoring.webp',
        ),
    ),
    39 => array(
        array(
            'source'   => 'auto-start-mt5-on-vps-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'auto-start-mt5-on-vps-cover-bg.png',
        ),
        array(
            'source'   => 'auto-start-mt5-on-vps-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'auto-start-mt5-on-vps-staggered-startup.webp',
        ),
        array(
            'source'   => 'auto-start-mt5-on-vps-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'auto-start-mt5-on-vps-task-scheduler-reboot-test.webp',
        ),
        array(
            'source'   => 'auto-start-mt5-on-vps-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'auto-start-mt5-on-vps-post-restart-validation.webp',
        ),
    ),
    40 => array(
        array(
            'source'   => 'does-ea-run-after-rdp-disconnect-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'does-ea-run-after-rdp-disconnect-cover-bg.png',
        ),
        array(
            'source'   => 'does-ea-run-after-rdp-disconnect-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'does-ea-run-after-rdp-disconnect-session-state-differences.webp',
        ),
        array(
            'source'   => 'does-ea-run-after-rdp-disconnect-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'does-ea-run-after-rdp-disconnect-safe-rdp-disconnect.webp',
        ),
        array(
            'source'   => 'does-ea-run-after-rdp-disconnect-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'does-ea-run-after-rdp-disconnect-vps-heartbeat-monitoring.webp',
        ),
    ),
    41 => array(
        array(
            'source'   => 'vps-mt5-ea-troubleshooting-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'vps-mt5-ea-troubleshooting-cover-bg.png',
        ),
        array(
            'source'   => 'vps-mt5-ea-troubleshooting-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'vps-mt5-ea-troubleshooting-preserve-evidence-before-restart.webp',
        ),
        array(
            'source'   => 'vps-mt5-ea-troubleshooting-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'vps-mt5-ea-troubleshooting-five-layer-fault-isolation.webp',
        ),
        array(
            'source'   => 'vps-mt5-ea-troubleshooting-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'vps-mt5-ea-troubleshooting-incident-correction-retest.webp',
        ),
    ),
    42 => array(
        array(
            'source'   => 'secure-vps-for-mt5-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'secure-vps-for-mt5-cover-bg.png',
        ),
        array(
            'source'   => 'secure-vps-for-mt5-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'secure-vps-for-mt5-strong-account-security.webp',
        ),
        array(
            'source'   => 'secure-vps-for-mt5-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'secure-vps-for-mt5-restricted-access-backup.webp',
        ),
        array(
            'source'   => 'secure-vps-for-mt5-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'secure-vps-for-mt5-backup-restore-drill.webp',
        ),
    ),
    43 => array(
        array(
            'source'   => 'broker-vps-vs-rented-vps-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'broker-vps-vs-rented-vps-cover-bg.png',
        ),
        array(
            'source'   => 'broker-vps-vs-rented-vps-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'broker-vps-vs-rented-vps-hosting-service-models.webp',
        ),
        array(
            'source'   => 'broker-vps-vs-rented-vps-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'broker-vps-vs-rented-vps-managed-vps-support.webp',
        ),
        array(
            'source'   => 'broker-vps-vs-rented-vps-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'broker-vps-vs-rented-vps-side-by-side-vps-test.webp',
        ),
    ),
    44 => array(
        array(
            'source'   => 'verify-ea-seller-before-buying-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'verify-ea-seller-before-buying-cover-bg.png',
        ),
        array(
            'source'   => 'verify-ea-seller-before-buying-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'verify-ea-seller-before-buying-seller-identity-documents.webp',
        ),
        array(
            'source'   => 'verify-ea-seller-before-buying-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'verify-ea-seller-before-buying-performance-evidence-chain.webp',
        ),
        array(
            'source'   => 'verify-ea-seller-before-buying-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'verify-ea-seller-before-buying-test-seller-support.webp',
        ),
    ),
    45 => array(
        array(
            'source'   => 'ea-license-account-device-lock-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'ea-license-account-device-lock-cover-bg.png',
        ),
        array(
            'source'   => 'ea-license-account-device-lock-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'ea-license-account-device-lock-account-vs-device-license.webp',
        ),
        array(
            'source'   => 'ea-license-account-device-lock-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'ea-license-account-device-lock-prepare-vps-migration.webp',
        ),
        array(
            'source'   => 'ea-license-account-device-lock-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'ea-license-account-device-lock-license-server-dependency.webp',
        ),
    ),
    46 => array(
        array(
            'source'   => 'ea-refund-license-support-terms-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'ea-refund-license-support-terms-cover-bg.png',
        ),
        array(
            'source'   => 'ea-refund-license-support-terms-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'ea-refund-license-support-terms-delivery-refund-license-support.webp',
        ),
        array(
            'source'   => 'ea-refund-license-support-terms-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'ea-refund-license-support-terms-read-purchase-terms.webp',
        ),
        array(
            'source'   => 'ea-refund-license-support-terms-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'ea-refund-license-support-terms-archive-purchase-evidence.webp',
        ),
    ),
    47 => array(
        array(
            'source'   => 'verify-ea-profit-screenshots-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'verify-ea-profit-screenshots-cover-bg.png',
        ),
        array(
            'source'   => 'verify-ea-profit-screenshots-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'verify-ea-profit-screenshots-cropped-screenshot-context.webp',
        ),
        array(
            'source'   => 'verify-ea-profit-screenshots-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'verify-ea-profit-screenshots-compare-evidence-sources.webp',
        ),
        array(
            'source'   => 'verify-ea-profit-screenshots-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'verify-ea-profit-screenshots-traceable-evidence-chain.webp',
        ),
    ),
    48 => array(
        array(
            'source'   => 'ea-without-stop-loss-risks-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'ea-without-stop-loss-risks-cover-bg.png',
        ),
        array(
            'source'   => 'ea-without-stop-loss-risks-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'ea-without-stop-loss-risks-broker-stop-vs-local-memory.webp',
        ),
        array(
            'source'   => 'ea-without-stop-loss-risks-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'ea-without-stop-loss-risks-tail-risk-distribution.webp',
        ),
        array(
            'source'   => 'ea-without-stop-loss-risks-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'ea-without-stop-loss-risks-operational-dependency-failure.webp',
        ),
    ),
    49 => array(
        array(
            'source'   => 'detect-grid-martingale-ea-results-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'detect-grid-martingale-ea-results-cover-bg.png',
        ),
        array(
            'source'   => 'detect-grid-martingale-ea-results-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'detect-grid-martingale-ea-results-chronological-lot-pattern.webp',
        ),
        array(
            'source'   => 'detect-grid-martingale-ea-results-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'detect-grid-martingale-ea-results-balance-vs-equity-divergence.webp',
        ),
        array(
            'source'   => 'detect-grid-martingale-ea-results-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'detect-grid-martingale-ea-results-extreme-market-stress-test.webp',
        ),
    ),
    50 => array(
        array(
            'source'   => 'after-buying-ea-checklist-contact-sheet.png',
            'quadrant' => 1,
            'kind'     => 'cover',
            'output'   => 'after-buying-ea-checklist-cover-bg.png',
        ),
        array(
            'source'   => 'after-buying-ea-checklist-contact-sheet.png',
            'quadrant' => 2,
            'output'   => 'after-buying-ea-checklist-ea-delivery-kit.webp',
        ),
        array(
            'source'   => 'after-buying-ea-checklist-contact-sheet.png',
            'quadrant' => 3,
            'output'   => 'after-buying-ea-checklist-demo-acceptance-test.webp',
        ),
        array(
            'source'   => 'after-buying-ea-checklist-contact-sheet.png',
            'quadrant' => 4,
            'output'   => 'after-buying-ea-checklist-secure-operations-handoff.webp',
        ),
    ),
);

/**
 * Open PNG or JPEG source files.
 */
function fenix_v3_open_image(string $filename): GdImage
{
    $extension = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
    $image     = match ($extension) {
        'png'        => imagecreatefrompng($filename),
        'jpg', 'jpeg' => imagecreatefromjpeg($filename),
        default      => false,
    };

    if (!$image instanceof GdImage) {
        throw new RuntimeException("Unable to open image: {$filename}");
    }

    return $image;
}

/**
 * Extract one quadrant while trimming the contact-sheet gutter.
 */
function fenix_v3_extract_quadrant(GdImage $source, int $quadrant): GdImage
{
    if ($quadrant < 1 || $quadrant > 4) {
        throw new InvalidArgumentException("Quadrant must be between 1 and 4.");
    }

    $width   = imagesx($source);
    $height  = imagesy($source);
    $halfW   = intdiv($width, 2);
    $halfH   = intdiv($height, 2);
    $inset   = max(5, (int) round(min($width, $height) * 0.006));
    $isRight = in_array($quadrant, array(2, 4), true);
    $isLower = in_array($quadrant, array(3, 4), true);
    $x       = ($isRight ? $halfW : 0) + $inset;
    $y       = ($isLower ? $halfH : 0) + $inset;
    $right   = ($isRight ? $width : $halfW) - $inset;
    $bottom  = ($isLower ? $height : $halfH) - $inset;
    $cropW   = max(1, $right - $x);
    $cropH   = max(1, $bottom - $y);
    $panel   = imagecreatetruecolor($cropW, $cropH);

    imagecopy($panel, $source, 0, 0, $x, $y, $cropW, $cropH);

    return $panel;
}

/**
 * Center-crop and resize an image to the article ratio.
 */
function fenix_v3_resize_cover(GdImage $source, int $width = 1200, int $height = 675): GdImage
{
    $sourceWidth  = imagesx($source);
    $sourceHeight = imagesy($source);
    $sourceRatio  = $sourceWidth / $sourceHeight;
    $targetRatio  = $width / $height;

    if ($sourceRatio > $targetRatio) {
        $cropHeight = $sourceHeight;
        $cropWidth  = (int) round($sourceHeight * $targetRatio);
        $sourceX    = (int) floor(($sourceWidth - $cropWidth) / 2);
        $sourceY    = 0;
    } else {
        $cropWidth  = $sourceWidth;
        $cropHeight = (int) round($sourceWidth / $targetRatio);
        $sourceX    = 0;
        $sourceY    = (int) floor(($sourceHeight - $cropHeight) / 2);
    }

    $canvas = imagecreatetruecolor($width, $height);
    imagecopyresampled(
        $canvas,
        $source,
        0,
        0,
        $sourceX,
        $sourceY,
        $width,
        $height,
        $cropWidth,
        $cropHeight
    );

    return $canvas;
}

$processed = 0;
$lastId    = $startId + $limit;

foreach ($jobs as $articleId => $articleJobs) {
    if ($articleId < $startId || $articleId >= $lastId) {
        continue;
    }

    foreach ($articleJobs as $job) {
        $sourceFile = $sourceDir . '/' . $job['source'];
        $isCover    = ($job['kind'] ?? 'article') === 'cover';
        $targetFile = ($isCover ? $coverDir : $outputDir) . '/' . $job['output'];

        if (!is_file($sourceFile)) {
            throw new RuntimeException("Missing source: {$sourceFile}");
        }

        $source = fenix_v3_open_image($sourceFile);
        $panel  = isset($job['quadrant'])
            ? fenix_v3_extract_quadrant($source, (int) $job['quadrant'])
            : $source;
        $output = fenix_v3_resize_cover($panel, 1200, $isCover ? 630 : 675);

        if ($panel !== $source) {
            imagedestroy($panel);
        }
        imagedestroy($source);

        $written = $isCover
            ? imagepng($output, $targetFile, 7)
            : imagewebp($output, $targetFile, 86);

        if (!$written) {
            imagedestroy($output);
            throw new RuntimeException("Unable to write output: {$targetFile}");
        }

        imagedestroy($output);
        ++$processed;
        fwrite(STDOUT, sprintf("Processed %02d %s\n", $articleId, $job['output']));
    }
}

fwrite(STDOUT, "Processed {$processed} article image(s).\n");

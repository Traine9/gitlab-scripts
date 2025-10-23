<?php
include __DIR__ . '/../../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$configPath = $argv[2] ?? __DIR__ . '/' . pathinfo(__FILE__, PATHINFO_FILENAME) . '.yml';
$args = YAML::parseFile($configPath);

$client = new Gitlab\Client();
$client->setUrl($args['url']);
$client->authenticate($args['token'], Gitlab\Client::AUTH_OAUTH_TOKEN);

exec("cd {$args['projectDir']} && git branch --show-current", $out);
$currentBranch = $out[0];

$mergeMode = $argv[1] ?? null;

createMergeRequest(
    $client,
    $currentBranch,
    $args['to'],
    "",
    '',
    72,
    (int)$args['projectId'],
    $mergeMode
);

function createMergeRequest(Gitlab\Client $api, $from, $to, $message, $release, $assignee, $projectId, $mergeMode)
{
    $taskNumber = str_replace($to, '', $from);
    $taskNumber = preg_replace('/^-/', '', $taskNumber);
    $taskNumber = strtoupper($taskNumber);
    $title = "$taskNumber $message";

    // Determine merge settings based on mode
    $mergeSettings = [];
    if ($mergeMode && preg_match('/^[01]+$/', $mergeMode)) {
        // Parse each digit as a flag (1 = auto-merge, 0 = no auto-merge)
        $digits = str_split($mergeMode);
        foreach ($digits as $digit) {
            $mergeSettings[] = $digit === '1';
        }
        print PHP_EOL . "Auto merge pattern: $mergeMode";
    } else {
        // No auto-merge for any branch
        $mergeSettings = array_fill(0, count($to), false);
        if ($mergeMode) {
            print PHP_EOL . "Invalid merge pattern. Use binary digits (e.g., 110, 011, 1010)";
        }
    }

    foreach ($to as $index => $toBranch) {
        $mergeWhenPipelineSucceeds = $mergeSettings[$index] ?? false;

        // Check if merge request already exists
        $existingMR = findExistingMergeRequest($api, $projectId, $from, $toBranch);
        if ($existingMR) {
            // Update existing merge request
            $api->mergeRequests()->merge($projectId, $existingMR['iid'], [
                'auto_merge' => $mergeWhenPipelineSucceeds
            ]);

            print PHP_EOL . "Updated existing MR from $from to $toBranch (auto-merge: " . ($mergeWhenPipelineSucceeds ? 'ON' : 'OFF') . ")";
        } else {
            // Create new merge request
            $api->mergeRequests()->create(
                $projectId,
                $from,
                $toBranch,
                $title,
                [
                    'assignee_id' => (int)$assignee,
                    'remove_source_branch' => $toBranch === 'master',
                    'merge_when_pipeline_succeeds' => $mergeWhenPipelineSucceeds
                ]
            );
            print PHP_EOL . "Created MR from $from to $toBranch (auto-merge: " . ($mergeWhenPipelineSucceeds ? 'ON' : 'OFF') . ")";
        }
    }
}

function findExistingMergeRequest(Gitlab\Client $api, $projectId, $sourceBranch, $targetBranch)
{

    $mergeRequests = $api->mergeRequests()->all($projectId, [
        'state' => 'opened',
        'source_branch' => $sourceBranch,
        'target_branch' => $targetBranch
    ]);

    return !empty($mergeRequests) ? $mergeRequests[0] : null;

}

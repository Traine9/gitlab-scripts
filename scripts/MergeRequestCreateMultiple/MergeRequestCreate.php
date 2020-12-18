<?php
include __DIR__.'/../../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;
$configPath = __DIR__.'/'.pathinfo(__FILE__, PATHINFO_FILENAME).'.yml';
$args = YAML::parseFile($configPath);

$client = new Gitlab\Client();
$client->setUrl($args['url']);
$client->authenticate($args['token'], Gitlab\Client::AUTH_OAUTH_TOKEN);

exec("cd {$args['projectDir']} && git branch --show current", $out);
$currentBranch = $out[0];

createMergeRequest(
    $client,
    $currentBranch,
    ["dev", "staging", "master"],
    $argv[2] ?? '',
    $argv[1],
    72,
    (int) $args['projectId']
);
function createMergeRequest(Gitlab\Client $api, $from, $to, $message, $release, $assignee, $projectId) {
    $taskNumber = str_replace($to, '', $from);
    $taskNumber = preg_replace('/^-/', '', $taskNumber);
    $taskNumber = strtoupper($taskNumber);
    $title = "$taskNumber $message [release $release]";

    foreach ($to as $toBranch) {
        $api->mergeRequests()->create($projectId, $from, $toBranch, $title, ['assignee_id' => (int) $assignee, 'remove_source_branch' => $to === 'master']);
        print PHP_EOL . "Merge request from $from $toBranch created";
    }
}



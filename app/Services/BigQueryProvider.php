<?php

namespace App\Services;

use Google\Cloud\BigQuery\BigQueryClient;

class BigQueryProvider
{
    protected BigQueryClient $client;
    protected string $projectId;
    protected string $dataset;
    protected string $table;

    public function __construct()
    {
        $this->projectId = env('BIGQUERY_PROJECT_ID', '');
        $this->dataset   = env('BIGQUERY_DATASET', '');
        $this->table     = env('BIGQUERY_TABLE', '');
        $keyFilePath     = env('BIGQUERY_KEYFILE', '');

        $config = ['projectId' => $this->projectId];
        if ($keyFilePath) { $config['keyFilePath'] = $keyFilePath; }
        $this->client = new BigQueryClient($config);
    }

    /**
     * Fetch rows with coordinates and optional categorical features.
     * Returns [[lat, lon, edo, mpio, hora, causaacci, tipaccid], ...]
     */
    public function fetchPoints(int $limit = 10000): array
    {
        if (!$this->projectId || !$this->dataset || !$this->table) { return []; }
        $query = sprintf(
            'SELECT lat, lon, EDO, MPIO, HORA, CAUSAACCI, TIPACCID FROM `%s.%s.%s` LIMIT %d',
            $this->projectId,
            $this->dataset,
            $this->table,
            $limit
        );
        $job = $this->client->runQuery($this->client->query($query));
        $rows = [];
        foreach ($job->rows() as $row) {
            $lat = isset($row['lat']) ? (float)str_replace(',', '.', (string)$row['lat']) : null;
            $lon = isset($row['lon']) ? (float)str_replace(',', '.', (string)$row['lon']) : null;
            if ($lat === null || $lon === null) { continue; }
            $rows[] = [
                $lat,
                $lon,
                $row['EDO'] ?? null,
                $row['MPIO'] ?? null,
                $row['HORA'] ?? null,
                $row['CAUSAACCI'] ?? null,
                $row['TIPACCID'] ?? null,
            ];
        }
        return $rows;
    }
}

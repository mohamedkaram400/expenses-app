<?php

declare(strict_types = 1);

function getTransactionFiles(string $dirPath): array
{
    $files = [];

    foreach (scandir($dirPath) as $file) {
        if (is_dir($file)) {
            continue;
        }

        $files[] = $dirPath . $file;
    }

    return $files;
}

function getTransactions(string $fileName): array {
    if (! file_exists($fileName)) {
        trigger_error('File ' . $fileName . 'not exists');
        return [];
    }

    $file = fopen($fileName, 'r');

    fgetcsv($file);

    $transactions = [];
    
    while (($transaction = fgetcsv($file)) !== false) {
        $transactions[] = readTransactions($transaction);
    }
    fclose($file);

    return $transactions;
}


function readTransactions(array $transactionRow): array {

    [$date, $chekNumber, $description, $amount] = $transactionRow;

    $amount = str_replace(['$', ','], '', $amount);

    return [
        'date'  => $date,
        'chekNumber'  => $chekNumber,
        'description'  => $description,
        'amount'  => $amount,
    ];
}

function calculateTotals(array $transactions): array
{
    $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    foreach ($transactions as $transaction) {
        $totals['netTotal'] += $transaction['amount'];

        if ($transaction['amount'] >= 0) {
            $totals['totalIncome'] += $transaction['amount'];
        } else {
            $totals['totalExpense'] += $transaction['amount'];
        }
    }

    return $totals;
}
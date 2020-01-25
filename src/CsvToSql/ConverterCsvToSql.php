<?php

namespace TForce\CsvToSql;

use TForce\Exceptions\TForceException;

class ConverterCsvToSql
{

    private const TABLES_ORDER = [
        1 => 'notices_types',
        2 => 'categories',
        3 => 'cities',
        4 => 'users',
        5 => 'favorites',
        6 => 'users_media',

        7  => 'users_notices_types',
        8  => 'users_categories',
        9  => 'notices',
        10 => 'tasks',
        11 => 'tasks_media',
        12 => 'responses',
        13 => 'reviews',
        14 => 'chat_msgs'
    ];
    private const DB_NAME = 'tforce';
    private const EXTENSION_CSV = 'csv';
    private const EXTENSION_SQL = 'sql';
    private const DELIMITER_CSV = ',';
    private const ENCLOSURE_CSV = '"';
    private const ESCAPE_CSV = '\\';
    private static $ROOT_FOLDER;
    private static $dirNameWithCSVFiles;
    private static $dirNameWithSQLFiles;

    /**
     * @param string $nameFolderWithCSV
     * @param string $nameFolderWithSQL
     */
    public function __construct(
        $nameFolderWithCSV = 'data',
        $nameFolderWithSQL = 'sql'
    )
    {
        self::$ROOT_FOLDER = dirname(getcwd());
        self::$dirNameWithCSVFiles =
            self::$ROOT_FOLDER . DIRECTORY_SEPARATOR . $nameFolderWithCSV;
        self::$dirNameWithSQLFiles =
            self::$ROOT_FOLDER . DIRECTORY_SEPARATOR . $nameFolderWithSQL;
    }

    /**
     * @param string $string
     * @return string
     */
    public static function wrapInBackQuotes(string $string): string
    {
        return '`' . $string . '`';
    }

    /**
     * @param string $pathToFolderWithCSV
     * @return array all paths to CSV files
     */
    public function getAllPathsOfCSVFiles(string $pathToFolderWithCSV): array
    {
        $dirWithCSV = new \DirectoryIterator($pathToFolderWithCSV);
        $arrOfAbsPathsToCSVFiles = [];

        foreach ($dirWithCSV as $itemOfDirWithCSV) {
            if (
                $itemOfDirWithCSV->isFile()
                &&
                $itemOfDirWithCSV->getExtension() === self::EXTENSION_CSV
            ) {
                $arrOfAbsPathsToCSVFiles[] = $itemOfDirWithCSV->getPathname();
            }

        }
        return $arrOfAbsPathsToCSVFiles;
    }

    /**
     *
     */
    public function handleDirWithCSVFiles(): void
    {
        $absPathsToCSVFiles =
            $this->getAllPathsOfCSVFiles(ConverterCsvToSql::$dirNameWithCSVFiles);

        foreach ($absPathsToCSVFiles as $oneAbsPathToCSVFile) {
            $dataFromCSVFile = $this->parseOneCSVFile($oneAbsPathToCSVFile);
            $this->writeSQLFile($dataFromCSVFile);
        }
    }

    /**
     * @param \SplFileObject $infoCSVFile
     */
    private function initCSVSettings(\SplFileObject $infoCSVFile): void
    {
        $infoCSVFile->setCsvControl(
            self::DELIMITER_CSV, self::ENCLOSURE_CSV, self::ESCAPE_CSV
        );

        $infoCSVFile->setFlags(
            \SplFileObject::READ_CSV
            | \SplFileObject::READ_AHEAD
            | \SplFileObject::SKIP_EMPTY
            | \SplFileObject::DROP_NEW_LINE
        );
    }

    /**
     * @param string $absPathToCSVFile
     * @return array
     * @throws TForceException
     */
    public function parseOneCSVFile(string $absPathToCSVFile): array
    {
        $infoCSVFile = null;

        try {
            $infoCSVFile = new \SplFileObject($absPathToCSVFile, 'r');
        } catch (\Exception $exception) {
            $exceptionMsg = 'Can not open CSV file. ' . $exception->getMessage();
            throw new TForceException($exceptionMsg);
        }

        $shortFileName = $infoCSVFile->getBasename('.' . self::EXTENSION_CSV);

        $this->initCSVSettings($infoCSVFile);
        $columnNames = $infoCSVFile->fgetcsv();
        $columnLength = count($columnNames);

        $allDataLines = [];

        while (!$infoCSVFile->eof()) {
            $arrData = $infoCSVFile->fgetcsv();
            if ($arrData) {
                $allDataLines[] = array_slice($arrData, 0, $columnLength);
            }
        }

        return compact('shortFileName', 'columnNames', 'allDataLines');
    }

    /**
     * @param array $dataFromCSVFile
     * @throws TForceException
     */
    public function writeSQLFile(array $dataFromCSVFile): void
    {
        list(
            'shortFileName' => $tableName,
            'columnNames' => $columnNames,
            'allDataLines' => $allDataLines
            ) = $dataFromCSVFile;

        if (count($allDataLines) === 0) {
            return;
        }

        $columnLength = count($columnNames);
        $columnNames = array_map([self::class, 'wrapInBackQuotes'], $columnNames);
        $sqlColumnNames = ' (' . implode(', ', $columnNames) . ') ';
        $insertStart =
            'INSERT INTO '
            . self::wrapInBackQuotes(self::DB_NAME)
            . '.'
            . self::wrapInBackQuotes($tableName)
            . $sqlColumnNames
            . "VALUES \r\n";
        $insertEnd = ';';

        // --- check if file exist - delete it

        $orderedFileName = $tableName;

        if (array_search($tableName, self::TABLES_ORDER)) {
            $orderedFileName =
                $tableName . '_' . array_search($tableName, self::TABLES_ORDER);
        }

        $sqlFileName = self::$dirNameWithSQLFiles
            . DIRECTORY_SEPARATOR
            . $orderedFileName
            . '.'
            . self::EXTENSION_SQL;

        if (file_exists($sqlFileName) && !unlink($sqlFileName)) {
            throw new TForceException(
                'Can not remove file ' . $sqlFileName
            );
        }

        // --- start writing Insert Phrase ( INSERT INTO ... VALUES)
        try {
            $sqlFile = new \SplFileObject($sqlFileName, "w");
        } catch (\Exception $exception) {
            $exceptionMsg = 'Error of attempt to write text string in file '
                . $exception->getMessage();
            throw new TForceException($exceptionMsg);
        }

        $sqlFile->fwrite($insertStart);

        // --- middle writing Insert Phrase  (...(value1, value2), ...)
        $iteratorWithValues =
            $this->getIteratorOfValues($allDataLines, $columnLength);

        foreach ($iteratorWithValues as $oneSQLLineWithValues) {
            $sqlFile->fwrite($oneSQLLineWithValues);
        }

        // --- end writing Insert Phrase (... ;)

        // delete final comma after last VALUES
        $sqlFile->fseek(-4, SEEK_END);
        $sqlFile->fwrite($insertEnd);
        $sqlFile = null;

    }

    /**
     * @param array $allDataLines
     * @param int $columnLength
     * @return \Generator|null
     * @throws TForceException
     */
    private function getIteratorOfValues(array $allDataLines, int $columnLength)
    {
        foreach ($allDataLines as $oneLine) {
            if (count($oneLine) !== $columnLength) {
                throw new TForceException(
                    'Count of items in 1 line !== count of column names! '
                );
            }

            $oneLineSQL = ' (' . implode(', ', $oneLine) . "), \r\n";
            yield $oneLineSQL;

        }
        return null;
    }

}





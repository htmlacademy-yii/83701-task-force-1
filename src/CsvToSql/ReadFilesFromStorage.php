<?php

class ConverterCsvToSql
{
    public const DB_NAME = 'tforce';
    public const EXTENSION_CSV = 'csv';
    public const EXTENSION_SQL = 'sql';
    public const DELIMITER_CSV = ',';
    public const ENCLOSURE_CSV = '"';
    public const ESCAPE_CSV = '\\';
    public static $ROOT_FOLDER;
    public static $dirNameWithCSVFiles;

    public function __construct($nameFolderWithCSV = 'data')
    {
        self::$ROOT_FOLDER = dirname(getcwd());
        self::$dirNameWithCSVFiles =
            self::$ROOT_FOLDER . DIRECTORY_SEPARATOR . $nameFolderWithCSV;
    }

    public static function wrapInBackQuotes($string): string
    {
        return '`' . $string . '`';
    }

    public function getAllPathsOfCSVFiles($pathToFolderWithCSV)
    {

        $dirWithCSV = new DirectoryIterator($pathToFolderWithCSV);
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

    public function handleDirWithCSVFiles()
    {
        $absPathsToCSVFiles =
            $this->getAllPathsOfCSVFiles(ConverterCsvToSql::$dirNameWithCSVFiles);

        $dataFromCSVFile = $this->parseOneCSVFile($absPathsToCSVFiles[0]);

        $this->writeSQLFile($dataFromCSVFile);


    }

    private function initCSVSettings($infoCSVFile)
    {
        $infoCSVFile->setCsvControl(
            self::DELIMITER_CSV, self::ENCLOSURE_CSV, self::ESCAPE_CSV
        );

        $infoCSVFile->setFlags(
            SplFileObject::READ_CSV
            | SplFileObject::READ_AHEAD
            | SplFileObject::SKIP_EMPTY
            | SplFileObject::DROP_NEW_LINE
        );
    }

    public function parseOneCSVFile($absPathToCSVFile)
    {
        $infoCSVFile = null;

        try {
            $infoCSVFile = new SplFileObject($absPathToCSVFile, 'r');
        } catch (\Exception $exception) {
            $exceptionMsg = 'Can not open CSV file. ' . $exception->getMessage();
            throw new Exception($exceptionMsg);
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

    public function writeSQLFile($dataFromCSVFile)
    {
        list(
            'shortFileName' => $tableName,
            'columnNames' => $columnNames,
            'allDataLines' => $allDataLines
            ) = $dataFromCSVFile;

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
        $sqlFileName = self::$dirNameWithCSVFiles
            . DIRECTORY_SEPARATOR
            . $tableName
            . '.'
            . self::EXTENSION_SQL;

        if (file_exists($sqlFileName) && !unlink($sqlFileName)) {
            throw new Error(
                'Can not remove file ' . $sqlFileName
            );
        }

        // --- start writing Insert Phrase ( INSERT INTO ... VALUES)
        try {
            $sqlFile = new SplFileObject($sqlFileName, "w");
        } catch (Exception $exception) {
            throw new Exception(
                'Error of attempt to write text string in file '
            );
        }

        $sqlFile->fwrite($insertStart);

        // --- middle writing Insert Phrase  (...(value1, value2)...)
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

    private function getIteratorOfValues($allDataLines, $columnLength)
    {

        foreach ($allDataLines as $oneLine) {
            if (count($oneLine) !== $columnLength) {
                throw new Exception(
                    'Count of items in 1 line !== count of column names! '
                );
            }

            $oneLineSQL = ' (' . implode(', ', $oneLine) . "), \r\n";

            yield $oneLineSQL;

        }
        return null;
    }

}

$converter = new ConverterCsvToSql();
$converter->handleDirWithCSVFiles();



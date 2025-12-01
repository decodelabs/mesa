# Mesa — Package Specification

> **Cluster:** `io`
> **Language:** `php`
> **Milestone:** `m5`
> **Repo:** `https://github.com/decodelabs/mesa`
> **Role:** CSV parser

## Overview

### Purpose

Mesa provides CSV parsing and generation capabilities for PHP applications. It enables reading and processing CSV/TSV files with a structured, object-oriented API. Mesa supports:

- CSV and TSV file parsing
- Structured access to workbooks, sheets, rows, and cells
- Column alias mapping for named access
- Row iteration and filtering
- Cell value access with raw and processed values
- Memory-efficient streaming parsing
- Integration with Atlas for file handling

Mesa is designed to provide a clean, type-safe way to work with CSV/TSV data in PHP applications, with support for named column access and flexible data processing.

### Non-Goals

- Mesa does not provide Excel file support (XLSX, XLS)
- It does not provide database import/export functionality
- It does not handle complex data transformations
- It does not provide data validation or sanitization
- It does not support writing CSV files (currently read-only)
- It does not handle multi-sheet workbooks (single sheet per file for CSV/TSV)

## Role in the Ecosystem

### Cluster & Positioning

Mesa belongs to the **io** cluster, providing file-based data input/output capabilities. It sits alongside other IO packages and is used for parsing structured text data files.

### Usage Contexts

Mesa is used for:

- CSV/TSV file parsing and processing
- Data import from spreadsheets
- Bulk data processing
- Data transformation pipelines
- Report generation from CSV data
- Data migration and ETL operations

## Public Surface

### Key Types

- **`Workbook`** — Interface representing a workbook containing multiple sheets. Provides access to sheets by name.

- **`Reader`** — Class implementing `Workbook` for reading CSV/TSV files. Handles file loading and format detection.

- **`Sheet`** — Interface representing a single sheet in a workbook.

- **`Reader\Sheet`** — Interface extending `Sheet` for readable sheets. Provides row iteration and scanning capabilities.

- **`Reader\SheetAbstract`** — Abstract class providing base implementation for sheet readers.

- **`Reader\Sheet\Csv`** — Class for reading CSV files with comma separator.

- **`Reader\Sheet\Tsv`** — Class for reading TSV files with tab separator.

- **`Row`** — Class representing a single row in a sheet. Implements `ArrayAccess` for cell access.

- **`Cell`** — Interface representing a single cell in a row. Provides raw and processed value access.

- **`Cell\Raw`** — Class implementing `Cell` for raw string values.

- **`CellTrait`** — Trait providing default cell implementation.

- **`AliasMap`** — Class for mapping column names to indices, enabling named column access.

- **`Writer\FeatureRegister`** — Class for managing writer features (currently unused, reserved for future writing functionality).

### Main Entry Points

- **`Reader::loadFile(string|File $file, ?string $format): Reader`** — Loads a CSV/TSV file and creates a reader instance. Auto-detects format from file extension if not provided.

- **`Reader::loadString(string $string, ?string $format, ?string $fileName): Reader`** — Loads CSV/TSV data from a string and creates a reader instance.

- **`Reader::getSheet(string $name): ?Sheet`** — Gets a sheet by name. Returns null if not found.

- **`Reader::firstSheet: ?Sheet`** — Property that returns the first sheet in the workbook.

- **`Sheet::scan(int|array|AliasMap|null $aliases, ?callable $filter): Generator<Row>`** — Scans rows in the sheet. Supports alias mapping and row filtering.

- **`Sheet::getIterator(): Generator<Row>`** — Returns an iterator for iterating over rows.

- **`Row::get(int|string $index): mixed`** — Gets a cell value by index or alias.

- **`Row::getRaw(int|string $index): ?string`** — Gets a cell raw value by index or alias.

- **`Row::getCell(int|string $index): ?Cell`** — Gets a cell object by index or alias.

- **`Row::has(int|string $index): bool`** — Checks if a cell exists and has a value.

- **`Row::isEmpty(): bool`** — Checks if the row is empty (all cells are null).

- **`Row::toArray(): array`** — Converts the row to an array with alias keys.

- **`Cell::rawValue: ?string`** — Property containing the raw cell value.

- **`Cell::value: mixed`** — Property containing the processed cell value.

- **`Cell::fromValue(mixed $value): static`** — Static factory method for creating a cell from a value.

- **`AliasMap::get(string $alias): ?int`** — Gets the index for a column alias.

- **`AliasMap::resolve(int $index): ?string`** — Gets the alias for a column index.

## Dependencies

### Decode Labs

- **`atlas`** — Used for file handling and memory file creation.

- **`coercion`** — Used for type coercion when converting cell values.

- **`exceptional`** — Used for exception handling throughout the package.

- **`nuance`** — Used for type inspection via `Dumpable` interface on `Row`, `AliasMap`, and `Writer\FeatureRegister`.

### External

- None (pure PHP implementation)

## Behaviour & Contracts

### Invariants

- CSV files use comma (`,`) as separator and double quote (`"`) as enclosure
- TSV files use tab (`\t`) as separator
- Empty strings in CSV are converted to null
- Rows are indexed starting from 0
- Cells maintain both raw and processed values
- Alias maps are case-sensitive
- Sheets are parsed lazily during iteration
- File handles are managed by Atlas

### Input & Output Contracts

- **`Reader::loadFile(string|File $file, ?string $format): Reader`** — Loads a file and creates a reader. Throws `NotFound` if file doesn't exist. Throws `ComponentUnavailable` if format is unsupported. Returns reader instance.

- **`Reader::loadString(string $string, ?string $format, ?string $fileName): Reader`** — Loads data from string. Throws `ComponentUnavailable` if format is unsupported. Returns reader instance.

- **`Reader::getSheet(string $name): ?Sheet`** — Returns sheet by name or null if not found.

- **`Sheet::scan(int|array|AliasMap|null $aliases, ?callable $filter): Generator<Row>`** — Scans rows. If `aliases` is an integer, uses that row as header. If array, creates alias map. If `AliasMap`, uses it directly. Filter callback receives row and returns row or null to skip. Yields rows.

- **`Row::get(int|string $index): mixed`** — Returns cell value. Throws `InvalidArgument` if alias not found. Returns null if cell doesn't exist.

- **`Row::getRaw(int|string $index): ?string`** — Returns cell raw value. Throws `InvalidArgument` if alias not found. Returns null if cell doesn't exist.

- **`Row::getCell(int|string $index): ?Cell`** — Returns cell object. Throws `InvalidArgument` if alias not found. Returns null if cell doesn't exist.

- **`Row::has(int|string $index): bool`** — Returns true if cell exists and has non-null raw value, false otherwise.

- **`Row::isEmpty(): bool`** — Returns true if all cells have null raw values, false otherwise.

- **`Row::toArray(): array`** — Returns array with alias keys (or numeric indices if no aliases) and cell values.

- **`Cell::rawValue: ?string`** — Returns raw string value from CSV.

- **`Cell::value: mixed`** — Returns processed value (may be different from raw value for typed cells).

- **`AliasMap::get(string $alias): ?int`** — Returns column index for alias, or numeric value if alias is numeric string, or null if not found.

- **`AliasMap::resolve(int $index): ?string`** — Returns alias for column index, or null if not found.

## Error Handling

Mesa uses the Exceptional pattern for error handling. Key exception types:

- **`NotFound`** — Thrown when a file cannot be found during loading.

- **`ComponentUnavailable`** — Thrown when an unsupported file format is requested.

- **`InvalidArgument`** — Thrown when an invalid column index or alias is used.

Exceptions preserve the original service context and include detailed error messages.

## Configuration & Extensibility

### Extension Points

- **Custom Sheet Formats** — Implement `Reader\Sheet` interface to support additional file formats.

- **Custom Cell Types** — Implement `Cell` interface to provide typed cell values (e.g., integer, date, boolean).

- **Custom Row Processing** — Use filter callbacks in `scan()` to process or transform rows.

### Configuration

- **Format Detection** — Format is auto-detected from file extension (`.csv` → CSV, `.tsv` → TSV) or can be specified explicitly.

- **Separator and Enclosure** — CSV uses comma separator and double quote enclosure. TSV uses tab separator. These are constants in sheet classes.

- **Alias Mapping** — Aliases can be provided as array, `AliasMap` instance, or integer (for header row index).

- **Row Filtering** — Filter callbacks can be provided to `scan()` to skip or transform rows.

## Interactions with Other Packages

- **Atlas** — Used for file handling, file access, and memory file creation.

- **Coercion** — Used for type coercion when converting cell values.

- **Exceptional** — Used for exception handling throughout the package.

- **Nuance** — Used for type inspection via `Dumpable` interface on `Row`, `AliasMap`, and `Writer\FeatureRegister`.

## Usage Examples

### Basic File Reading

```php
use DecodeLabs\Mesa\Reader;

// Load from file
$reader = Reader::loadFile('data.csv');

// Get first sheet
$sheet = $reader->firstSheet;

// Iterate over rows
foreach ($sheet as $row) {
    echo $row->get(0); // Get first column
    echo $row->get(1); // Get second column
}
```

### Named Column Access

```php
use DecodeLabs\Mesa\Reader;
use DecodeLabs\Mesa\AliasMap;

$reader = Reader::loadFile('data.csv');
$sheet = $reader->firstSheet;

// Use first row as header
foreach ($sheet->scan(0) as $row) {
    echo $row->get('name'); // Access by column name
    echo $row->get('email');
}

// Or provide aliases explicitly
$aliases = new AliasMap(['name', 'email', 'age']);
foreach ($sheet->scan($aliases) as $row) {
    echo $row->get('name');
    echo $row->get('age');
}
```

### Row Filtering

```php
use DecodeLabs\Mesa\Reader;

$reader = Reader::loadFile('data.csv');
$sheet = $reader->firstSheet;

// Filter out empty rows
foreach ($sheet->scan(null, fn($row) => $row->isEmpty() ? null : $row) as $row) {
    // Process non-empty rows
}

// Filter by condition
foreach ($sheet->scan(null, function($row) {
    return $row->get('status') === 'active' ? $row : null;
}) as $row) {
    // Process active rows only
}
```

### String Input

```php
use DecodeLabs\Mesa\Reader;

$csv = "name,email\nJohn,john@example.com\nJane,jane@example.com";
$reader = Reader::loadString($csv, 'csv');

$sheet = $reader->firstSheet;
foreach ($sheet->scan(0) as $row) {
    echo $row->get('name');
    echo $row->get('email');
}
```

### TSV Files

```php
use DecodeLabs\Mesa\Reader;

// TSV format is auto-detected from .tsv extension
$reader = Reader::loadFile('data.tsv');

// Or specify explicitly
$reader = Reader::loadFile('data.txt', 'tsv');
```

### Row to Array Conversion

```php
use DecodeLabs\Mesa\Reader;

$reader = Reader::loadFile('data.csv');
$sheet = $reader->firstSheet;

foreach ($sheet->scan(0) as $row) {
    $data = $row->toArray(); // ['name' => 'John', 'email' => 'john@example.com']
}
```

### Cell Access

```php
use DecodeLabs\Mesa\Reader;

$reader = Reader::loadFile('data.csv');
$sheet = $reader->firstSheet;

foreach ($sheet as $row) {
    // Get processed value
    $value = $row->get(0);
    
    // Get raw value
    $raw = $row->getRaw(0);
    
    // Get cell object
    $cell = $row->getCell(0);
    $cellValue = $cell->value;
    $cellRaw = $cell->rawValue;
}
```

### Multiple Sheets

```php
use DecodeLabs\Mesa\Reader;

$reader = Reader::loadFile('data.csv');

// Get sheet by name
$sheet = $reader->getSheet('Sheet 1');

// Iterate all sheets
foreach ($reader->sheets as $name => $sheet) {
    echo "Processing sheet: $name\n";
    foreach ($sheet as $row) {
        // Process rows
    }
}
```

## Implementation Notes (for Contributors)

### Architecture

- **Lazy Parsing** — Sheets are parsed lazily during iteration, allowing memory-efficient processing of large files.

- **Streaming** — Row reading uses file streams, processing one row at a time rather than loading entire file into memory.

- **Format Detection** — Format is detected from file extension or can be specified explicitly. Format classes are resolved via namespace.

- **Cell Abstraction** — Cells maintain both raw and processed values, allowing typed cell implementations.

- **Alias Mapping** — Alias maps enable named column access while maintaining numeric index access.

- **Row Filtering** — Filter callbacks allow flexible row processing without loading all rows into memory.

- **Weak References** — Sheets use weak references to workbooks to avoid circular references.

- **ArrayAccess** — Rows implement `ArrayAccess` for convenient cell access via array syntax.

### Performance Considerations

- Streaming parsing avoids loading entire file into memory
- Lazy sheet parsing defers work until needed
- Weak references prevent memory leaks
- Generator-based iteration provides efficient memory usage

### Design Decisions

- **Format-Specific Classes** — Using separate classes for CSV and TSV allows format-specific customization.

- **Cell Interface** — Separating raw and processed values allows typed cell implementations.

- **Alias Mapping** — Providing alias mapping enables named access while maintaining backward compatibility with numeric indices.

- **Row Filtering** — Using callbacks for filtering provides flexibility without additional API surface.

- **Generator-Based Iteration** — Using generators allows memory-efficient processing of large files.

- **Atlas Integration** — Using Atlas for file handling provides consistent file access across the ecosystem.

- **Nuance Integration** — Implementing `Dumpable` provides debugging and inspection capabilities.

## Testing & Quality

**Code Quality:** 3/5 — Functional codebase with good structure. Some areas may benefit from additional features or refinements.

**README Quality:** 1/5 — Minimal documentation with placeholder content.

**Documentation:** 0/5 — No formal documentation beyond README.

**Tests:** 0/5 — No test suite currently.

See `composer.json` for supported PHP versions.

## Roadmap & Future Ideas

- Enhanced documentation and API reference
- Test suite implementation
- CSV/TSV writing functionality
- Additional file format support
- Performance optimizations
- Typed cell implementations
- Data validation integration
- Schema-based parsing
- Large file handling improvements

## References

- [Decode Labs Chorus](https://github.com/decodelabs/chorus)
- [Mesa Repository](https://github.com/decodelabs/mesa)


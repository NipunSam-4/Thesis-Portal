/**
 * Global Excel Sheet Preview Utility using SheetJS
 */

const SHEETJS_CDN_URL = 'https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js';

let sheetJsLoadingPromise = null;

/**
 * Dynamically loads the SheetJS library if not already loaded.
 * @returns {Promise<void>}
 */
export function loadSheetJS() {
    if (window.XLSX) {
        return Promise.resolve(window.XLSX);
    }

    if (sheetJsLoadingPromise) {
        return sheetJsLoadingPromise;
    }

    sheetJsLoadingPromise = new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = SHEETJS_CDN_URL;
        script.async = true;
        script.onload = () => {
            if (window.XLSX) {
                resolve(window.XLSX);
            } else {
                reject(new Error('SheetJS loaded but XLSX is not available on window.'));
            }
        };
        script.onerror = () => {
            sheetJsLoadingPromise = null;
            reject(new Error('Failed to load SheetJS library from CDN.'));
        };
        document.head.appendChild(script);
    });

    return sheetJsLoadingPromise;
}

/**
 * Validates the first sheet against the template's 3 mandatory columns:
 * 1. Title of the paper
 * 2. Author Names (seperated by comma)
 * 3. Journal Name
 * 
 * @param {Array<Array<any>>} sheetData - 2D array of rows from sheet_to_json({ header: 1, raw: false })
 * @param {string} sheetName
 * @returns {{ isValid: boolean, errors: string[], titleCol: number, authorCol: number, journalCol: number, headerRowIdx: number, invalidCells: Set<string> }}
 */
function validatePublicationData(sheetData, sheetName) {
    const errors = [];
    const invalidCells = new Set(); // format: "rowIdx,colIdx"
    
    if (!sheetData || sheetData.length === 0) {
        return {
            isValid: false,
            errors: [`Sheet "${sheetName}" is completely empty.`],
            titleCol: -1,
            authorCol: -1,
            journalCol: -1,
            headerRowIdx: -1,
            invalidCells
        };
    }

    // Find header row (first row containing recognizable header names)
    let headerRowIdx = -1;
    let titleCol = -1;
    let authorCol = -1;
    let journalCol = -1;

    for (let r = 0; r < Math.min(sheetData.length, 5); r++) {
        const row = sheetData[r] || [];
        for (let c = 0; c < row.length; c++) {
            const rawVal = String(row[c] || '').trim().toLowerCase();
            if (titleCol === -1 && (rawVal.includes('title of the paper') || rawVal.includes('title of paper') || rawVal.includes('paper title') || rawVal === 'title')) {
                titleCol = c;
            }
            if (authorCol === -1 && (rawVal.includes('author name') || rawVal.includes('authors') || rawVal.includes('author'))) {
                authorCol = c;
            }
            if (journalCol === -1 && (rawVal.includes('journal name') || rawVal.includes('journal'))) {
                journalCol = c;
            }
        }
        if (titleCol !== -1 || authorCol !== -1 || journalCol !== -1) {
            headerRowIdx = r;
            break;
        }
    }

    if (headerRowIdx === -1) {
        headerRowIdx = 0;
        const row0 = sheetData[0] || [];
        for (let c = 0; c < row0.length; c++) {
            const rawVal = String(row0[c] || '').trim().toLowerCase();
            if (titleCol === -1 && (rawVal.includes('title') || rawVal.includes('paper'))) titleCol = c;
            if (authorCol === -1 && (rawVal.includes('author') || rawVal.includes('name'))) authorCol = c;
            if (journalCol === -1 && rawVal.includes('journal')) journalCol = c;
        }
    }

    // Verify presence of mandatory column headers
    if (titleCol === -1) {
        errors.push(`Sheet "${sheetName}": Missing mandatory column "Title of the paper".`);
    }
    if (authorCol === -1) {
        errors.push(`Sheet "${sheetName}": Missing mandatory column "Author Names".`);
    }
    if (journalCol === -1) {
        errors.push(`Sheet "${sheetName}": Missing mandatory column "Journal Name".`);
    }

    // Validate data rows
    let dataRowCount = 0;
    for (let r = headerRowIdx + 1; r < sheetData.length; r++) {
        const row = sheetData[r] || [];
        // Check if row is entirely empty
        const hasContent = row.some(cell => String(cell || '').trim() !== '');
        if (!hasContent) continue; // Skip blank rows

        dataRowCount++;
        const rowNumber = r + 1; // 1-indexed for display

        if (titleCol !== -1) {
            const val = String(row[titleCol] || '').trim();
            if (!val) {
                errors.push(`Sheet "${sheetName}", Row ${rowNumber}: "Title of the paper" is mandatory and cannot be empty.`);
                invalidCells.add(`${r},${titleCol}`);
            }
        }

        if (authorCol !== -1) {
            const val = String(row[authorCol] || '').trim();
            if (!val) {
                errors.push(`Sheet "${sheetName}", Row ${rowNumber}: "Author Names" is mandatory and cannot be empty.`);
                invalidCells.add(`${r},${authorCol}`);
            }
        }

        if (journalCol !== -1) {
            const val = String(row[journalCol] || '').trim();
            if (!val) {
                errors.push(`Sheet "${sheetName}", Row ${rowNumber}: "Journal Name" is mandatory and cannot be empty.`);
                invalidCells.add(`${r},${journalCol}`);
            }
        }
    }

    return {
        isValid: errors.length === 0,
        errors,
        titleCol,
        authorCol,
        journalCol,
        headerRowIdx,
        invalidCells
    };
}

/**
 * Builds an HTML table preserving the original Excel formatting (bold, italic, underline, etc.)
 * using XLSX.utils.sheet_to_html, and highlights mandatory asterisks in red and errors on invalid cells.
 */
function buildValidatedHtmlTable(worksheet, validation, sheetIndex = 0) {
    // Generate native HTML table from SheetJS preserving original cell formats
    const rawHtml = window.XLSX.utils.sheet_to_html(worksheet, {
        id: 'sheet-table-' + sheetIndex,
        editable: false
    });

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = rawHtml;
    const table = tempDiv.querySelector('table');

    if (!table) {
        return rawHtml || '<p class="text-xs text-gray-500 italic p-4">No content in this sheet.</p>';
    }

    const { titleCol, authorCol, journalCol, headerRowIdx, invalidCells } = validation;
    const rows = table.querySelectorAll('tr');

    // For sheet 0, decorate the 3 mandatory headers with red asterisk
    if (sheetIndex === 0 && headerRowIdx >= 0 && headerRowIdx < rows.length) {
        const headerRow = rows[headerRowIdx];
        const cells = headerRow.children;

        const mandatoryIndices = [titleCol, authorCol, journalCol].filter(idx => idx !== undefined && idx >= 0);

        mandatoryIndices.forEach(idx => {
            if (idx < cells.length) {
                const cell = cells[idx];
                const originalText = cell.innerText || cell.textContent || '';
                const cleanText = originalText.replace(/\*/g, '').trim();
                cell.innerHTML = `${cleanText} <span class="text-red-500 font-bold text-sm" title="Mandatory Field">*</span>`;
            }
        });
    }

    // Highlight only missing mandatory cells in data rows
    invalidCells.forEach(cellCoord => {
        const [rStr, cStr] = cellCoord.split(',');
        const r = parseInt(rStr, 10);
        const c = parseInt(cStr, 10);

        if (r >= 0 && r < rows.length) {
            const row = rows[r];
            const cells = row.children;
            if (c >= 0 && c < cells.length) {
                const cell = cells[c];
                cell.className += ' bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-200 font-semibold border-2 border-red-400';
                cell.innerHTML = `
                    <span class="italic text-xs flex items-center">
                        <svg class="w-3.5 h-3.5 mr-1 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        [Missing Mandatory Value]
                    </span>
                `;
            }
        }
    });

    return tempDiv.innerHTML;
}

/**
 * Renders an XLSX workbook object into standard preview DOM elements.
 * 
 * @param {object} workbook - Parsed SheetJS workbook.
 * @param {object} [options]
 * @param {string} [options.containerId='excelPreviewContainer']
 * @param {string} [options.sheetsOutputId='sheetsOutput']
 * @param {string} [options.sheetTabsBarId='sheetTabsBar']
 * @param {string} [options.sheetCountId='excelSheetCount']
 * @param {string} [options.titleId='excelPreviewTitle']
 * @param {string} [options.titlePrefix='Publication and Other Recognition Preview']
 * @param {boolean} [options.validatePublications=true]
 * @returns {{ isValid: boolean, errors: string[] }}
 */
export function renderExcelPreview(workbook, options = {}) {
    const {
        containerId = 'excelPreviewContainer',
        sheetsOutputId = 'sheetsOutput',
        sheetTabsBarId = 'sheetTabsBar',
        sheetCountId = 'excelSheetCount',
        titleId = 'excelPreviewTitle',
        titlePrefix = 'Publication and Other Recognition Preview',
        validatePublications = true
    } = options;

    const container = document.getElementById(containerId);
    const sheetsOutput = document.getElementById(sheetsOutputId);
    const sheetTabsBar = document.getElementById(sheetTabsBarId);
    const sheetCountSpan = document.getElementById(sheetCountId);
    const titleSpan = document.getElementById(titleId);

    if (!container || !sheetsOutput) return { isValid: true, errors: [] };

    sheetsOutput.innerHTML = '';
    if (sheetTabsBar) sheetTabsBar.innerHTML = '';
    container.classList.remove('hidden');

    if (titleSpan) titleSpan.innerText = titlePrefix;

    const sheetNames = workbook.SheetNames || [];
    if (sheetCountSpan) {
        sheetCountSpan.innerText = `${sheetNames.length} Sheet(s) Found`;
    }

    let allErrors = [];

    // Validate only the FIRST sheet
    const sheetValidations = {};
    if (validatePublications && sheetNames.length > 0) {
        const firstSheetName = sheetNames[0];
        const worksheet = workbook.Sheets[firstSheetName];
        if (worksheet) {
            const sheetData = window.XLSX.utils.sheet_to_json(worksheet, { header: 1, raw: false, defval: '' });
            const valResult = validatePublicationData(sheetData, firstSheetName);
            sheetValidations[firstSheetName] = valResult;
            if (!valResult.isValid) {
                allErrors = allErrors.concat(valResult.errors);
            }
        }
    }

    // Render error summary banner if validation failed
    if (allErrors.length > 0) {
        const alertBox = document.createElement('div');
        alertBox.className = 'p-4 bg-red-50 dark:bg-red-950/50 border-2 border-red-300 dark:border-red-800 rounded-xl space-y-2 shadow-sm mb-4';
        alertBox.innerHTML = `
            <div class="flex items-center text-sm font-bold text-red-700 dark:text-red-300">
                <svg class="w-5 h-5 mr-2 text-red-600 dark:text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span>Mandatory Publication Fields Missing in First Sheet (${sheetNames[0]}):</span>
            </div>
            <p class="text-xs text-red-600 dark:text-red-300">
                Mandatory fields marked with an asterisk (*) in the first sheet must be filled for every entry.
            </p>
            <ul class="list-disc list-inside text-xs text-red-700 dark:text-red-300 space-y-1 pl-1 font-medium max-h-40 overflow-y-auto">
                ${allErrors.slice(0, 10).map(err => `<li>${err}</li>`).join('')}
                ${allErrors.length > 10 ? `<li>... and ${allErrors.length - 10} more error(s)</li>` : ''}
            </ul>
        `;
        sheetsOutput.appendChild(alertBox);
    }

    sheetNames.forEach((sheetName, index) => {
        const worksheet = workbook.Sheets[sheetName];
        if (!worksheet) return;

        // Only first sheet (index === 0) has validation rules applied; other sheets display as-is
        const valResult = (index === 0 && sheetValidations[sheetName]) ? sheetValidations[sheetName] : {
            isValid: true,
            errors: [],
            mandatoryCols: new Map(),
            headerRowIdx: -1,
            invalidCells: new Set()
        };

        const htmlTable = buildValidatedHtmlTable(worksheet, valResult, index);

        // Create Sheet Tab Button if tab bar exists
        if (sheetTabsBar) {
            const tabBtn = document.createElement('a');
            tabBtn.href = `#sheet-block-${index}`;
            tabBtn.className = `px-3 py-1.5 text-xs font-bold rounded-lg border transition flex items-center shrink-0 ${
                valResult.isValid 
                    ? 'border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/60' 
                    : 'border-red-300 dark:border-red-800 bg-red-50 dark:bg-red-950/40 text-red-700 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-950/60'
            }`;
            tabBtn.innerHTML = `<span class="w-2 h-2 rounded-full ${valResult.isValid ? 'bg-emerald-500' : 'bg-red-500'} mr-1.5"></span> ${sheetName}`;
            sheetTabsBar.appendChild(tabBtn);
        }

        // Create Sheet Block Container
        const sheetBlock = document.createElement('div');
        sheetBlock.id = `sheet-block-${index}`;
        sheetBlock.className = 'bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm space-y-3';
        sheetBlock.innerHTML = `
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-800 pb-2">
                <h5 class="font-bold text-sm text-indigo-800 dark:text-indigo-300 uppercase tracking-wider flex items-center">
                    <span class="w-2.5 h-2.5 rounded-full ${valResult.isValid ? 'bg-emerald-500' : 'bg-red-500'} mr-2"></span>
                    Sheet (${index + 1}/${sheetNames.length}): ${sheetName}
                </h5>
                <span class="text-xs ${index === 0 ? (valResult.isValid ? 'text-emerald-600 dark:text-emerald-400 font-semibold' : 'text-red-600 dark:text-red-400 font-bold') : 'text-gray-500 dark:text-gray-400 font-medium'}">
                    ${index === 0 ? (valResult.isValid ? '✓ Validated' : '⚠ Validation Errors Found') : 'Preview Mode'}
                </span>
            </div>
            <div class="overflow-x-auto sheet-table-container">
                ${htmlTable}
            </div>
        `;
        sheetsOutput.appendChild(sheetBlock);
    });

    return {
        isValid: allErrors.length === 0,
        errors: allErrors
    };
}

/**
 * Reads a user-uploaded File object, parses it with SheetJS, and renders the preview.
 * 
 * @param {File} file - Excel file from input[type="file"].
 * @param {object} [options]
 * @returns {Promise<{ isValid: boolean, errors: string[], workbook: object }>}
 */
export async function previewExcelFile(file, options = {}) {
    if (!file) return { isValid: true, errors: [] };

    await loadSheetJS();

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = window.XLSX.read(data, { type: 'array', cellDates: false, cellText: true, raw: false });
                const valResult = renderExcelPreview(workbook, options);
                resolve({ ...valResult, workbook });
            } catch (err) {
                console.error('Error parsing Excel file:', err);
                reject(err);
            }
        };
        reader.onerror = (err) => reject(err);
        reader.readAsArrayBuffer(file);
    });
}

/**
 * Fetches an Excel document from a URL and renders the preview.
 * 
 * @param {string} url - Document URL.
 * @param {object} [options]
 * @returns {Promise<{ isValid: boolean, errors: string[], workbook: object }>}
 */
export async function previewExcelUrl(url, options = {}) {
    if (!url) return { isValid: true, errors: [] };

    await loadSheetJS();

    try {
        const res = await fetch(url);
        if (!res.ok) {
            console.warn(`Excel document could not be fetched from ${url} (Status: ${res.status})`);
            return { isValid: false, errors: [`Document file not found (${res.status})`], workbook: null };
        }
        const ab = await res.arrayBuffer();
        const data = new Uint8Array(ab);
        const workbook = window.XLSX.read(data, { type: 'array', cellDates: false, cellText: true, raw: false });
        const valResult = renderExcelPreview(workbook, options);
        return { ...valResult, workbook };
    } catch (err) {
        console.warn('Error previewing Excel from URL:', err);
        return { isValid: false, errors: [err.message || 'Error loading Excel preview'], workbook: null };
    }
}

/**
 * Clears and hides the Excel preview container.
 * 
 * @param {object} [options]
 * @param {string} [options.containerId='excelPreviewContainer']
 * @param {string} [options.sheetsOutputId='sheetsOutput']
 * @param {string} [options.sheetTabsBarId='sheetTabsBar']
 */
export function clearExcelPreview(options = {}) {
    const {
        containerId = 'excelPreviewContainer',
        sheetsOutputId = 'sheetsOutput',
        sheetTabsBarId = 'sheetTabsBar'
    } = options;

    const container = document.getElementById(containerId);
    const sheetsOutput = document.getElementById(sheetsOutputId);
    const sheetTabsBar = document.getElementById(sheetTabsBarId);

    if (container) container.classList.add('hidden');
    if (sheetsOutput) sheetsOutput.innerHTML = '';
    if (sheetTabsBar) sheetTabsBar.innerHTML = '';
}

// Attach helpers to global window for accessibility from any Blade view or Alpine.js component
if (typeof window !== 'undefined') {
    window.loadSheetJS = loadSheetJS;
    window.renderExcelPreview = renderExcelPreview;
    window.previewExcelFile = previewExcelFile;
    window.previewExcelUrl = previewExcelUrl;
    window.clearExcelPreview = clearExcelPreview;
}

<?php
// Shared PDF styles — loaded in all templates via: <?php include __DIR__ . '/_styles.php'; ?>
// Defines: color palette, spacing scale, typography, common classes
?>
<style>
/* ══════════════════════════════════════════════════════════════════════
   DESIGN TOKENS (reference only — mPDF does not support CSS custom
   properties/var(), so every value below is hardcoded literally at each
   use site across _styles.php and the *.php templates instead of a live
   :root block. Change a value here AND everywhere it's used if a token
   needs to change; there is no single source of truth to edit.

   Color palette:   --pdf-primary #1e3a6e | --pdf-secondary #64748b |
                     --pdf-border #e2e8f0 | --pdf-text #1e293b |
                     --pdf-text-muted #94a3b8 | --pdf-text-light #6b7280 |
                     --pdf-bg-light #f8fafc | --pdf-bg-lighter #f3f4f6
   Accent colors:    --pdf-success #10b981 | --pdf-error #ef4444 |
                     --pdf-warning #fcd34d | --pdf-info #2563eb
   Spacing (8px base): --pdf-space-0 0 | -1 2px | -2 4px | -3 6px | -4 8px |
                     -5 10px | -6 12px | -7 14px | -8 16px | -9 18px | -10 20px
   Typography:       --pdf-font-sans 'Roboto','Helvetica',sans-serif |
                     --pdf-font-mono monospace | --pdf-text-xs 6.5pt |
                     --pdf-text-sm 8pt | --pdf-text-base 9pt |
                     --pdf-text-lg 9.5pt | --pdf-text-xl 10pt |
                     --pdf-text-2xl 14pt | --pdf-text-3xl 18pt
   ══════════════════════════════════════════════════════════════════════ */

/* ══════════════════════════════════════════════════════════════════════
   RESET & BASE STYLES
   ══════════════════════════════════════════════════════════════════════ */

* {
  margin: 0;
  padding: 0;
}

body {
  font-family: 'Roboto', 'Helvetica', sans-serif;
  color: #1e293b;
  font-size: 9pt;
}

table {
  width: 100%;
  border-collapse: collapse;
}

/* ══════════════════════════════════════════════════════════════════════
   COMMON COMPONENTS
   ══════════════════════════════════════════════════════════════════════ */

/* Footer strip (shared across all PDFs) */
.pdf-footer {
  border-top: 1px solid #e2e8f0;
  padding-top: 16px;
  font-size: 6.5pt;
  color: #94a3b8;
  text-align: center;
}

/* Section titles */
.pdf-title {
  font-size: 18pt;
  color: #1e293b;
  margin: 0;
  font-weight: 700;
  letter-spacing: -0.01em;
}

.pdf-subtitle {
  font-size: 8pt;
  color: #94a3b8;
  margin-top: 4px;
}

/* Labels (field names) */
.pdf-label {
  font-size: 6.5pt;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  margin-bottom: 2px;
}

/* Values (field content) */
.pdf-value {
  font-size: 9pt;
  color: #1e293b;
  font-weight: 500;
}

.pdf-value-small {
  font-size: 8pt;
  color: #1e293b;
  font-weight: 500;
}

/* Status badges */
.pdf-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 6.5pt;
  font-weight: 700;
  letter-spacing: 0.02em;
}

.pdf-badge.success {
  background: #dcfce7;
  color: #166534;
}

.pdf-badge.error {
  background: #fee2e2;
  color: #991b1b;
}

.pdf-badge.warning {
  background: #fef3c7;
  color: #92400e;
}

.pdf-badge.pending {
  background: #f8fafc;
  color: #475569;
}

/* Summary bar (key-value pairs) */
.pdf-summary-bar {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 16px;
}

.pdf-summary-bar td {
  padding: 12px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  vertical-align: middle;
}

/* Standard table */
.pdf-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

.pdf-table th {
  background: #1e3a6e;
  color: #ffffff;
  padding: 8px 6px;
  font-size: 8pt;
  text-align: left;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  border-bottom: 2px solid #1e3a6e;
}

.pdf-table td {
  padding: 8px;
  border-bottom: 1px solid #e2e8f0;
  font-size: 9pt;
  vertical-align: middle;
}

.pdf-table tr:nth-child(even) {
  background: #f8fafc;
}

/* Alternating row colors (explicit for PDFs) */
.pdf-row-even {
  background: #f8fafc;
}

.pdf-row-odd {
  background: #ffffff;
}

/* Header row */
.pdf-table-header {
  background: #1e3a6e;
  color: #ffffff;
}

.pdf-table-header td {
  padding: 8px 6px;
  font-size: 8pt;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  border-bottom: 2px solid #1e3a6e;
}

/* Section header row */
.pdf-section-header td {
  background: #1e3a6e;
  color: #ffffff;
  padding: 8px 10px;
  font-size: 9pt;
  font-weight: 700;
}

/* Signature area */
.pdf-signature-area {
  margin-top: 20px;
  width: 100%;
}

.pdf-signature-box {
  border-top: 1px solid #94a3b8;
  padding-top: 10px;
  text-align: center;
  font-size: 9pt;
}

/* Legal/footer text */
.pdf-legal {
  margin-top: 16px;
  padding-top: 10px;
  border-top: 1px solid #e2e8f0;
  font-size: 6.5pt;
  color: #94a3b8;
  text-align: center;
}

/* Grade circle (for nota-circle, etc) */
.pdf-grade-circle {
  display: inline-block;
  width: 30px;
  height: 30px;
  line-height: 30px;
  border-radius: 15px;
  text-align: center;
  font-weight: 700;
  color: #ffffff;
  font-size: 9pt;
}

.pdf-grade-circle.aprobado {
  background: #10b981;
}

.pdf-grade-circle.suspenso {
  background: #ef4444;
}

.pdf-grade-circle.vacio {
  color: #94a3b8;
}

.pdf-grade-circle.especial {
  background: #64748b;
  font-size: 6.5pt;
  width: 34px;
}

/* Break/recreation row */
.pdf-break-row {
  background: #f8fafc;
}

.pdf-break-row td {
  padding: 10px;
  text-align: center;
  font-weight: 500;
  color: #1e293b;
  font-size: 9pt;
}
</style>

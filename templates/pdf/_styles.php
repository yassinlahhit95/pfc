<?php
// Shared PDF styles — loaded in all templates via: <?php include __DIR__ . '/_styles.php'; ?>
// Defines: color palette, spacing scale, typography, common classes
?>
<style>
/* ══════════════════════════════════════════════════════════════════════
   DESIGN TOKENS
   ══════════════════════════════════════════════════════════════════════ */

:root {
  /* Color palette */
  --pdf-primary: #1e3a6e;
  --pdf-secondary: #64748b;
  --pdf-border: #e2e8f0;
  --pdf-text: #1e293b;
  --pdf-text-muted: #94a3b8;
  --pdf-text-light: #6b7280;
  --pdf-bg-light: #f8fafc;
  --pdf-bg-lighter: #f3f4f6;

  /* Accent colors */
  --pdf-success: #10b981;
  --pdf-error: #ef4444;
  --pdf-warning: #fcd34d;
  --pdf-info: #2563eb;

  /* Spacing scale (8px base) */
  --pdf-space-0: 0;
  --pdf-space-1: 2px;
  --pdf-space-2: 4px;
  --pdf-space-3: 6px;
  --pdf-space-4: 8px;
  --pdf-space-5: 10px;
  --pdf-space-6: 12px;
  --pdf-space-7: 14px;
  --pdf-space-8: 16px;
  --pdf-space-9: 18px;
  --pdf-space-10: 20px;

  /* Typography */
  --pdf-font-sans: 'Roboto', 'Helvetica', sans-serif;
  --pdf-font-mono: monospace;
  --pdf-text-xs: 6.5pt;
  --pdf-text-sm: 8pt;
  --pdf-text-base: 9pt;
  --pdf-text-lg: 9.5pt;
  --pdf-text-xl: 10pt;
  --pdf-text-2xl: 14pt;
  --pdf-text-3xl: 18pt;
}

/* ══════════════════════════════════════════════════════════════════════
   RESET & BASE STYLES
   ══════════════════════════════════════════════════════════════════════ */

* {
  margin: 0;
  padding: 0;
}

body {
  font-family: var(--pdf-font-sans);
  color: var(--pdf-text);
  font-size: var(--pdf-text-base);
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
  border-top: 1px solid var(--pdf-border);
  padding-top: var(--pdf-space-8);
  font-size: var(--pdf-text-xs);
  color: var(--pdf-text-muted);
  text-align: center;
}

/* Section titles */
.pdf-title {
  font-size: var(--pdf-text-3xl);
  color: var(--pdf-text);
  margin: 0;
  font-weight: 700;
  letter-spacing: -0.01em;
}

.pdf-subtitle {
  font-size: var(--pdf-text-sm);
  color: var(--pdf-text-muted);
  margin-top: var(--pdf-space-2);
}

/* Labels (field names) */
.pdf-label {
  font-size: var(--pdf-text-xs);
  color: var(--pdf-text-muted);
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  margin-bottom: var(--pdf-space-1);
}

/* Values (field content) */
.pdf-value {
  font-size: var(--pdf-text-base);
  color: var(--pdf-text);
  font-weight: 500;
}

.pdf-value-small {
  font-size: var(--pdf-text-sm);
  color: var(--pdf-text);
  font-weight: 500;
}

/* Status badges */
.pdf-badge {
  display: inline-block;
  padding: var(--pdf-space-2) var(--pdf-space-4);
  border-radius: 20px;
  font-size: var(--pdf-text-xs);
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
  background: var(--pdf-bg-light);
  color: #475569;
}

/* Summary bar (key-value pairs) */
.pdf-summary-bar {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: var(--pdf-space-8);
}

.pdf-summary-bar td {
  padding: var(--pdf-space-6);
  background: var(--pdf-bg-light);
  border: 1px solid var(--pdf-border);
  vertical-align: middle;
}

/* Standard table */
.pdf-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: var(--pdf-space-5);
}

.pdf-table th {
  background: var(--pdf-primary);
  color: #ffffff;
  padding: var(--pdf-space-4) var(--pdf-space-3);
  font-size: var(--pdf-text-sm);
  text-align: left;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  border-bottom: 2px solid var(--pdf-primary);
}

.pdf-table td {
  padding: var(--pdf-space-4);
  border-bottom: 1px solid var(--pdf-border);
  font-size: var(--pdf-text-base);
  vertical-align: middle;
}

.pdf-table tr:nth-child(even) {
  background: var(--pdf-bg-light);
}

/* Alternating row colors (explicit for PDFs) */
.pdf-row-even {
  background: var(--pdf-bg-light);
}

.pdf-row-odd {
  background: #ffffff;
}

/* Header row */
.pdf-table-header {
  background: var(--pdf-primary);
  color: #ffffff;
}

.pdf-table-header td {
  padding: var(--pdf-space-4) var(--pdf-space-3);
  font-size: var(--pdf-text-sm);
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  border-bottom: 2px solid var(--pdf-primary);
}

/* Section header row */
.pdf-section-header td {
  background: var(--pdf-primary);
  color: #ffffff;
  padding: var(--pdf-space-4) var(--pdf-space-5);
  font-size: var(--pdf-text-base);
  font-weight: 700;
}

/* Signature area */
.pdf-signature-area {
  margin-top: var(--pdf-space-10);
  width: 100%;
}

.pdf-signature-box {
  border-top: 1px solid var(--pdf-text-muted);
  padding-top: var(--pdf-space-5);
  text-align: center;
  font-size: var(--pdf-text-base);
}

/* Legal/footer text */
.pdf-legal {
  margin-top: var(--pdf-space-8);
  padding-top: var(--pdf-space-5);
  border-top: 1px solid var(--pdf-border);
  font-size: var(--pdf-text-xs);
  color: var(--pdf-text-muted);
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
  font-size: var(--pdf-text-base);
}

.pdf-grade-circle.aprobado {
  background: var(--pdf-success);
}

.pdf-grade-circle.suspenso {
  background: var(--pdf-error);
}

.pdf-grade-circle.vacio {
  color: var(--pdf-text-muted);
}

.pdf-grade-circle.especial {
  background: var(--pdf-secondary);
  font-size: var(--pdf-text-xs);
  width: 34px;
}

/* Break/recreation row */
.pdf-break-row {
  background: var(--pdf-bg-light);
}

.pdf-break-row td {
  padding: var(--pdf-space-5);
  text-align: center;
  font-weight: 500;
  color: var(--pdf-text);
  font-size: var(--pdf-text-base);
}
</style>

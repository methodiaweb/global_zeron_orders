<?php
/**
 * Template: My Account -> Obligations
 *
 * Variables:
 * - array $unpaid
 * - array $paid_by_year
 * - array $years
 * - string $selected_year
 * - string $mode
 */

declare(strict_types=1);

if (!defined('ABSPATH')) { exit; }

$mode = isset($mode) ? (string)$mode : 'dummy';

function gzo_format_date(string $ymd): string {
    $ts = strtotime($ymd);
    if (!$ts) { return esc_html($ymd); }
    $format = (string)get_option('date_format');
    return esc_html(date_i18n($format, $ts));
}

function gzo_format_amount(float $amount): string {
    // MVP: EUR only (later will be handled by a currency plugin).
    return esc_html(number_format($amount, 2, '.', ' ') . ' EUR');
}

function gzo_pdf_url(array $row): string {
    // MVP: Dummy opens a placeholder PDF endpoint inside WP (not ERP).
    // Later: replace with ERP PDF URL / proxy endpoint.
    $type = (string)($row['pdf_type'] ?? '');
    $doc = (string)($row['doc_number'] ?? '');
    $doc = rawurlencode($doc);

    return esc_url(add_query_arg([
        'gzo_dummy_pdf' => '1',
        'type' => $type,
        'doc' => $doc,
    ], home_url('/')));
}
?>

<div class="gzo-wrap">
    <div class="gzo-section">
        <h2 class="gzo-title"><?php echo esc_html__('Неплатени', 'global-zeron-orders'); ?></h2>

        <?php if ($mode === 'live'): ?>
            <div class="gzo-note">
                <?php echo esc_html__('Live режимът още не е вързан към ERP (MVP).', 'global-zeron-orders'); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($unpaid)): ?>
            <div class="gzo-empty"><?php echo esc_html__('Няма неплатени задължения.', 'global-zeron-orders'); ?></div>
        <?php else: ?>
            <div class="gzo-list">
                <?php foreach ($unpaid as $row): ?>
                    <?php
                        $has_pdf = !empty($row['pdf_available']);
                        $doc_type = (string)($row['doc_type'] ?? '');
                        $doc_number = (string)($row['doc_number'] ?? '');
                        $date = (string)($row['date'] ?? '');
                        $amount = (float)($row['amount_eur'] ?? 0);
                        $overdue = (int)($row['days_overdue'] ?? 0);
                    ?>
                    <div class="gzo-row">
                        <div class="gzo-col gzo-col-main">
                            <div class="gzo-doc">
                                <span class="gzo-doc-type"><?php echo esc_html($doc_type); ?></span>
                                <span class="gzo-doc-number"><?php echo esc_html($doc_number); ?></span>
                            </div>
                            <div class="gzo-meta">
                                <span><?php echo esc_html__('Дата:', 'global-zeron-orders'); ?> <?php echo gzo_format_date($date); ?></span>
                                <span><?php echo esc_html__('Просрочие (дни):', 'global-zeron-orders'); ?> <?php echo esc_html((string)$overdue); ?></span>
                            </div>
                        </div>
                        <div class="gzo-col gzo-col-amount">
                            <div class="gzo-amount"><?php echo gzo_format_amount($amount); ?></div>
                        </div>
                        <div class="gzo-col gzo-col-actions">
                            <?php if ($has_pdf): ?>
                                <a class="gzo-btn" href="<?php echo gzo_pdf_url($row); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html__('PDF', 'global-zeron-orders'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="gzo-section">
        <div class="gzo-header-row">
            <h2 class="gzo-title"><?php echo esc_html__('Платени', 'global-zeron-orders'); ?></h2>

            <?php if (!empty($years)): ?>
                <form method="get" class="gzo-year-form">
                    <?php
                        // Preserve other query args safely.
                        foreach ($_GET as $k => $v) {
                            if ($k === 'gzo_paid_year') { continue; }
                            if (is_array($v)) { continue; }
                            echo '<input type="hidden" name="' . esc_attr((string)$k) . '" value="' . esc_attr((string)$v) . '" />';
                        }
                    ?>
                    <label for="gzo_paid_year" class="gzo-year-label"><?php echo esc_html__('Година:', 'global-zeron-orders'); ?></label>
                    <select id="gzo_paid_year" name="gzo_paid_year" onchange="this.form.submit()">
                        <?php foreach ($years as $y): ?>
                            <option value="<?php echo esc_attr((string)$y); ?>" <?php selected($selected_year, (string)$y); ?>>
                                <?php echo esc_html((string)$y); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            <?php else: ?>
                <?php if ($mode === 'live'): ?>
                    <div class="gzo-note"><?php echo esc_html__('Годините ще идват от ERP номенклатура (по-късно).', 'global-zeron-orders'); ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php if (empty($paid_by_year)): ?>
            <div class="gzo-empty"><?php echo esc_html__('Няма платени документи за избраната година.', 'global-zeron-orders'); ?></div>
        <?php else: ?>
            <div class="gzo-list">
                <?php foreach ($paid_by_year as $row): ?>
                    <?php
                        $has_pdf = !empty($row['pdf_available']);
                        $doc_type = (string)($row['doc_type'] ?? '');
                        $doc_number = (string)($row['doc_number'] ?? '');
                        $date = (string)($row['date'] ?? '');
                        $amount = (float)($row['amount_eur'] ?? 0);
                        $overdue = (int)($row['days_overdue'] ?? 0);
                    ?>
                    <div class="gzo-row">
                        <div class="gzo-col gzo-col-main">
                            <div class="gzo-doc">
                                <span class="gzo-doc-type"><?php echo esc_html($doc_type); ?></span>
                                <span class="gzo-doc-number"><?php echo esc_html($doc_number); ?></span>
                            </div>
                            <div class="gzo-meta">
                                <span><?php echo esc_html__('Дата:', 'global-zeron-orders'); ?> <?php echo gzo_format_date($date); ?></span>
                                <span><?php echo esc_html__('Просрочие (дни):', 'global-zeron-orders'); ?> <?php echo esc_html((string)$overdue); ?></span>
                            </div>
                        </div>
                        <div class="gzo-col gzo-col-amount">
                            <div class="gzo-amount"><?php echo gzo_format_amount($amount); ?></div>
                        </div>
                        <div class="gzo-col gzo-col-actions">
                            <?php if ($has_pdf): ?>
                                <a class="gzo-btn" href="<?php echo gzo_pdf_url($row); ?>" target="_blank" rel="noopener">
                                    <?php echo esc_html__('PDF', 'global-zeron-orders'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

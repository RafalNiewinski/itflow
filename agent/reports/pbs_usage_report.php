<?php

require_once "includes/inc_all_reports.php";

enforceUserPermission('module_sales');

/**
 * Validate YYYY-MM-DD
 */
function isValidDateYmd($s) {
    return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s);
}

// Default range: current month
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to   = isset($_GET['to'])   ? $_GET['to']   : date('Y-m-t');

if (!isValidDateYmd($from)) $from = date('Y-m-01');
if (!isValidDateYmd($to))   $to   = date('Y-m-t');

// Inclusive datetime bounds
$from_dt = $from . " 00:00:00";
$to_dt   = $to   . " 23:59:59";

// Count number of days
$from_date = new DateTime($from);
$to_date = new DateTime($to);
$report_days = $from_date->diff($to_date)->days + 1;

$stmt = $mysqli->prepare("
    SELECT
        server_name,
        namespace_path,
        MIN(report_date) as first_report,
        MAX(report_date) as last_report,
        count(*) as report_count,
        MIN(unique_size_gib) as min_usage,
        MAX(unique_size_gib) as max_usage,
        ROUND(AVG(unique_size_gib), 3) as average_usage
    FROM pbs_usage_reports
    WHERE report_date BETWEEN ? AND ?
    GROUP BY namespace_path, server_name
");
$stmt->bind_param("ss", $from_dt, $to_dt);
$stmt->execute();
$result = $stmt->get_result();

?>
<div class="card">
    <div class="card-header bg-dark py-2">
        <h3 class="card-title mt-2">
            <i class="fas fa-fw fa-database mr-2"></i>
            Proxmox Backup Server Usage Report (<?php echo nullable_htmlentities($from); ?> to <?php echo nullable_htmlentities($to); ?>)
        </h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary d-print-none" onclick="window.print();">
                <i class="fas fa-fw fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <div class="card-header d-print-none">
        <!-- Filters -->
        <form class="mb-3">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="mb-1">From</label>
                    <input type="date" class="form-control" name="from" value="<?php echo nullable_htmlentities($from); ?>">
                </div>

                <div class="col-md-3 mb-2">
                    <label class="mb-1">To</label>
                    <input type="date" class="form-control" name="to" value="<?php echo nullable_htmlentities($to); ?>">
                </div>

                <div class="col-md-2 mb-2 d-flex align-items-end ml-auto">
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-fw fa-filter mr-1"></i>Apply
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive-sm">
        <table class="table table-striped table-sm">
            <thead class="bg-dark">
            <tr>
                <th>Server</th>
                <th>Backup Object</th>
                <th>First Report</th>
                <th>Last Report</th>
                <th>Report Count</th>
                <th class="text-right" style="width: 150px;">Minimum Usage</th>
                <th class="text-right" style="width: 150px;">Maximum Usage</th>
                <th class="text-right" style="width: 150px;">Average Usage</th>
            </tr>
            </thead>

            <tbody>
            <?php
            $had_rows = false;

            while ($r = mysqli_fetch_assoc($result)) {
                $had_rows = true;

                // Reply row (indented)
                ?>
                <tr>
                    <td><?php echo $r['server_name']; ?></td>
                    <td><?php echo $r['namespace_path'] ?></td>
                    <td><?php echo $r['first_report'] ?></td>
                    <td><?php echo $r['last_report'] ?></td>
                    <?php
                    if($r['report_count'] == $report_days) echo "<td>".$r['report_count']."</td>";
                    else if($r['report_count'] < $report_days) echo '<td style="color: red;" class="font-weight-bold">'.$r['report_count']." (missing reports)</td>";
                    else echo '<td style="color: green;" class="font-weight-bold">'.$r['report_count']." (extra reports)</td>";
                    ?>
                    <td class="text-right"><?php echo $r['min_usage'] ?> GiB</td>
                    <td class="text-right"><?php echo $r['max_usage'] ?> GiB</td>
                    <td class="text-right font-weight-bold"><?php echo $r['average_usage'] ?> GiB</td>
                </tr>
                <?php
            }

            if (!$had_rows) {
                ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        No PBS usage found for this date range.
                    </td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once "../../includes/footer.php";

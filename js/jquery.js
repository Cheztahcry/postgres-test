$(document).ready(function() {
    const $searchBar        = $('#search_bar');
    const $typeFilter       = $('#borrower_type_filter');
    const $viewMode         = $('#view_mode');
    const $reportSelector   = $('#report_selector');
    const $minCountFilter   = $('#min_count_filter');
    const $havingGroup      = $('#having_filter_group');
    const $searchButton     = $('#search-button');
    const $searchResults    = $('#search-results');
    const $studentDashboard = $('#student-dashboard');

    // Toggle HAVING input visibility based on view mode
    $viewMode.on('change', function() {
        if ($(this).val() === 'grouped' && $reportSelector.val() === 'search') {
            $havingGroup.css('display', 'flex');
        } else {
            $havingGroup.hide();
        }
        executeRequest();
    });

    $reportSelector.on('change', function() {
        const action = $(this).val();
        if (action === 'search') {
            $searchBar.show();
            $typeFilter.show();
            $viewMode.show();
            if ($viewMode.val() === 'grouped') {
                $havingGroup.css('display', 'flex');
            }
        } else {
            $havingGroup.hide();
        }
        executeRequest();
    });

    function executeRequest() {
        const action       = $reportSelector.val();
        const input        = $searchBar.val().trim();
        const borrowerType = $typeFilter.val();
        const viewMode     = $viewMode.val();
        const minCount     = $minCountFilter.val() || 1;

        // If default search mode with empty parameters, show master list
        if (action === 'search' && input === '' && borrowerType === '' && viewMode === 'detailed') {
            $searchResults.hide();
            $studentDashboard.show();
            return;
        }

        $.ajax({
            url: "classes/handlers/SearchHandler.php",
            method: "POST",
            data: {
                action: action,
                input: input,
                borrower_type: borrowerType,
                view_mode: viewMode,
                min_count: minCount
            },
            dataType: "json",
            success: function(response) {
                $studentDashboard.hide();
                $searchResults.show();

                const $thead     = $searchResults.find('table thead').empty();
                const $tbody     = $searchResults.find('table tbody').empty();
                const $noResults = $searchResults.find('.no-results-card');
                const $table     = $searchResults.find('table');

                const dataRows = response.payload ? (response.payload.data || response.payload) : (response.data || []);

                if (!dataRows || dataRows.length === 0) {
                    $table.hide();
                    $noResults.show();
                    return;
                }

                $table.show();
                $noResults.hide();

                // Dynamic Search Results Rendering
                if (action === 'search') {
                    if (response.mode === 'grouped') {
                        $thead.append(`
                            <tr>
                                <th>Borrower Type</th>
                                <th>Total Count (COUNT)</th>
                            </tr>
                        `);
                        $.each(dataRows, function(_, row) {
                            $tbody.append(`
                                <tr>
                                    <td>${row.category || ''}</td>
                                    <td><strong>${row.total_count || 0}</strong></td>
                                </tr>
                            `);
                        });
                    } else {
                        $thead.append(`
                            <tr>
                                <th>Borrower No.</th>
                                <th>Full Name</th>
                                <th>Borrower Type</th>
                                <th>Action</th>
                            </tr>
                        `);
                        $.each(dataRows, function(_, row) {
                            $tbody.append(`
                                <tr>
                                    <td>${row.borrower_uid || row.borrower_no || ''}</td>
                                    <td>${row.full_name || (row.first_name + ' ' + row.last_name)}</td>
                                    <td>${row.category || row.borrower_type || ''}</td>
                                    <td class="action-cell">
                                        <form action="borrowers_dashboard.php" method="POST">
                                            <button type="submit" name="borrower_no" class="action-btn inquire-btn" value="${row.borrower_uid || row.borrower_no || ''}">More Info</button>
                                        </form>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                    return;
                }

                // Universal Auto-Table Generator for Week 6 - 10 Reports
                const keys = Object.keys(dataRows[0]);
                let headerHtml = '<tr>';
                $.each(keys, function(_, key) {
                    const formattedKey = key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                    headerHtml += `<th>${formattedKey}</th>`;
                });
                headerHtml += '</tr>';
                $thead.append(headerHtml);

                $.each(dataRows, function(_, row) {
                    let rowHtml = '<tr>';
                    $.each(keys, function(_, key) {
                        rowHtml += `<td>${row[key] !== null ? row[key] : ''}</td>`;
                    });
                    rowHtml += '</tr>';
                    $tbody.append(rowHtml);
                });
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }

    $searchButton.on('click', executeRequest);
    $typeFilter.on('change', executeRequest);
    $searchBar.on('keyup', function(e) {
        if (e.key === 'Enter') {
            executeRequest();
        }
    });
});
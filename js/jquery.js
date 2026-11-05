$(document).ready(function() {
    const $searchBar = $('#search_bar');
    const $searchResults = $('#search-results');
    const $searchButton = $('#search-button');
    const $studentDashboard = $('#student-dashboard');

    // Reusable function to execute the AJAX search
    function performSearch() {
        const input = $searchBar.val().trim();

        if (input !== "") {
            $.ajax({
                url: "classes/handlers/SearchHandler.php",
                method: "POST",
                data: { input: input },
                dataType: "json",
                success: function(data) {
                    // Switch views: hide main dashboard, show search container
                    $studentDashboard.hide();
                    $searchResults.show();

                    const $tbody = $searchResults.find('table tbody').empty();
                    const $noResults = $searchResults.find('.no-results-card');
                    const $table = $searchResults.find('table');

                    if (!data || data.length === 0) {
                        $table.hide();
                        $noResults.show();
                        return;
                    }

                    $table.show();
                    $noResults.hide();

                    // Iterate over JSON array and construct table rows
                    $.each(data, function(index, row) {
                        const tr = `
                            <tr>
                                <td>${row.last_name || ''}</td>
                                <td>${row.first_name || ''}</td>
                                <td class="action-cell">
                                    <button type="button" class="action-btn inquire-btn">More Info</button>
                                </td>
                            </tr>
                        `;
                        $tbody.append(tr);
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Search AJAX Error:", error);
                }
            });
        } else {
            // Restore default view when input is cleared
            $searchResults.hide();
            $studentDashboard.show();
        }
    }
    // Trigger search on explicit button click
    $searchButton.on('click', performSearch);
});
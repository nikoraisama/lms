function updateContent() {
    fetch('ajax.php').then(response => response.json()).then(data => {
            //update monitoring table content
            let tableContent = "<tr><th>USN</th><th>Name</th><th>Course</th><th>Year Level</th><th>Type</th><th>Sign-in Time</th><th>Sign-out Time</th><th>Status</th></tr>";
            if (data.data.length > 0) {
                data.data.forEach(row => {
                    tableContent += `
                        <tr>
                            <td>${row.usn}</td>
                            <td>${row.name}</td>
                            <td>${row.course}</td>
                            <td>${row.year_level}</td>
                            <td>${row.type}</td>
                            <td>${new Date(row.in_time).toLocaleString()}</td>
                            <td>${row.status === 'Signed-out' ? new Date(row.out_time).toLocaleString() : '-'}</td>
                            <td>${row.status}</td>
                        </tr>`;
                });
            } else {
                tableContent += "<tr><td colspan='8' style='text-align: center;'>No data available for the current date.</td></tr>";
            }
            //update monitoring table
            document.getElementById('monitoring-table').innerHTML = tableContent;

            //update occupant count
            document.querySelector('.occupant-count').innerHTML = data.count;
        });
}

updateContent();
setInterval(updateContent, 1000);

<!DOCTYPE html>
<html>
<head>
    <title>Test Format</title>
</head>
<body>
    <script>
        // Test the format function
        const total = 120000;
        const formattedTotal = 'Rp ' + total.toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
        console.log('Formatted:', formattedTotal);
        document.write('<h3>Test Format Rupiah</h3>');
        document.write('<p><strong>Original:</strong> ' + total + '</p>');
        document.write('<p><strong>Formatted:</strong> ' + formattedTotal + '</p>');
    </script>
</body>
</html>
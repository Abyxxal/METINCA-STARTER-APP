<!DOCTYPE html>
<html>
<head>
    <title>Cascade Dropdown Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h2 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h3 { color: #555; margin-top: 30px; }
        table { border-collapse: collapse; width: 100%; margin: 15px 0; }
        td, th { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #007bff; color: white; font-weight: bold; }
        tr:nth-child(even) { background: #f9f9f9; }
        .ok { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .test-section { background: #fff3cd; padding: 20px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0; }
        select { width: 100%; max-width: 400px; padding: 10px; margin: 10px 0; font-size: 14px; border: 2px solid #007bff; border-radius: 4px; }
        button { padding: 10px 20px; margin: 5px; font-size: 14px; cursor: pointer; border: none; border-radius: 4px; color: white; font-weight: bold; }
        .btn-dept1 { background: #007bff; }
        .btn-dept3 { background: #28a745; }
        .btn-all { background: #6c757d; }
        button:hover { opacity: 0.8; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; margin: 15px 0; }
        .console-log { background: #2d2d2d; color: #00ff00; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace; margin: 15px 0; max-height: 300px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔍 CASCADE DROPDOWN TEST PAGE</h2>
        
        <div class="info">
            <strong>📋 Test Purpose:</strong> Verify cascade dropdown logic works correctly
        </div>

        <h3>📊 Database Data:</h3>
        
        <h4>Departments ({{ $departments->count() }})</h4>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
            </tr>
            @foreach($departments as $dept)
            <tr>
                <td>{{ $dept->id }}</td>
                <td>{{ $dept->name }}</td>
            </tr>
            @endforeach
        </table>
        <p class="ok">✓ Total: {{ $departments->count() }} departments</p>

        <h4>Divisions ({{ $divisions->count() }})</h4>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Department ID</th>
            </tr>
            @foreach($divisions as $div)
            <tr>
                <td>{{ $div->id }}</td>
                <td>{{ $div->name }}</td>
                <td>{{ $div->department_id }}</td>
            </tr>
            @endforeach
        </table>
        <p class="ok">✓ Total: {{ $divisions->count() }} divisions</p>

        <h4>Positions ({{ $positions->count() }})</h4>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Division ID</th>
            </tr>
            @foreach($positions as $pos)
            <tr>
                <td>{{ $pos->id }}</td>
                <td>{{ $pos->name }}</td>
                <td>{{ $pos->division_id }}</td>
            </tr>
            @endforeach
        </table>
        <p class="ok">✓ Total: {{ $positions->count() }} positions</p>

        <hr>

        <div class="test-section">
            <h3>🧪 INTERACTIVE TEST: Division Dropdown Filtering</h3>
            <p><strong>Instructions:</strong></p>
            <ol>
                <li>Click buttons below to filter divisions by department</li>
                <li>Watch the dropdown - options should appear/disappear</li>
                <li>Open browser Console (F12) to see detailed logs</li>
            </ol>

            <label><strong>Division Dropdown:</strong></label><br>
            <select id="testDivision">
                <option value="">-- Pilih Divisi --</option>
                @foreach($divisions as $div)
                <option value="{{ $div->id }}" data-department="{{ $div->department_id }}" style="display:none;">
                    {{ $div->name }}
                </option>
                @endforeach
            </select>

            <br><br>
            <button class="btn-dept1" onclick="showForDept(1)">Show for Dept 1 (Quality)</button>
            <button class="btn-dept3" onclick="showForDept(3)">Show for Dept 3 (SDM)</button>
            <button class="btn-all" onclick="showAll()">Show All Divisions</button>
            <button class="btn-all" onclick="hideAll()">Hide All</button>
        </div>

        <div id="logOutput" class="console-log">
            <div id="logs">Console logs will appear here...</div>
        </div>

        <div class="info">
            <strong>✅ Expected Behavior:</strong><br>
            - Click "Show for Dept 1" → Should show: Quality Assurance, Quality Control<br>
            - Click "Show for Dept 3" → Should show: HRD<br>
            - Click "Show All" → Should show all 6 divisions
        </div>
    </div>

    <script>
        // Custom console logger
        function log(msg, type = 'info') {
            const logsDiv = document.getElementById('logs');
            const timestamp = new Date().toLocaleTimeString();
            const color = type === 'success' ? '#00ff00' : type === 'error' ? '#ff6b6b' : '#00d4ff';
            logsDiv.innerHTML += `<div style="color:${color}">[${timestamp}] ${msg}</div>`;
            logsDiv.scrollTop = logsDiv.scrollHeight;
            console.log(msg);
        }

        function showForDept(deptId) {
            log(`🔍 Showing divisions for department: ${deptId}`, 'info');
            let count = 0;
            const options = document.querySelectorAll('#testDivision option');
            
            log(`📋 Total options found: ${options.length}`, 'info');
            
            options.forEach((opt, i) => {
                if (i === 0) return; // skip first placeholder
                
                const optDept = opt.getAttribute('data-department');
                const optValue = opt.value;
                const optText = opt.textContent;
                
                if (optDept == deptId) {
                    opt.style.display = '';
                    count++;
                    log(`  ✓ SHOW: [${optValue}] ${optText} (dept=${optDept})`, 'success');
                } else {
                    opt.style.display = 'none';
                    log(`  ✗ HIDE: [${optValue}] ${optText} (dept=${optDept})`, 'info');
                }
            });
            
            log(`✅ Total shown: ${count} divisions for dept ${deptId}`, 'success');
            alert(`Showed ${count} divisions for department ${deptId}.\nCheck dropdown and console logs above!`);
        }

        function showAll() {
            log('🔓 Showing ALL divisions', 'info');
            const options = document.querySelectorAll('#testDivision option');
            let count = 0;
            options.forEach((opt, i) => {
                if (i > 0) {
                    opt.style.display = '';
                    count++;
                }
            });
            log(`✅ Showed ${count} divisions`, 'success');
            alert(`All ${count} divisions are now visible!`);
        }

        function hideAll() {
            log('🔒 Hiding ALL divisions', 'info');
            const options = document.querySelectorAll('#testDivision option');
            let count = 0;
            options.forEach((opt, i) => {
                if (i > 0) {
                    opt.style.display = 'none';
                    count++;
                }
            });
            log(`✅ Hid ${count} divisions`, 'success');
        }

        // Initial page load
        window.onload = function() {
            log('🚀 TEST PAGE LOADED', 'success');
            const options = document.querySelectorAll('#testDivision option');
            log(`📊 Total option elements: ${options.length}`, 'info');
            
            options.forEach((opt, i) => {
                if (i > 0) {
                    const data = {
                        index: i,
                        value: opt.value,
                        text: opt.textContent,
                        dataDepartment: opt.getAttribute('data-department'),
                        currentDisplay: opt.style.display
                    };
                    log(`Option ${i}: value=${data.value}, text="${data.text}", dept=${data.dataDepartment}, display="${data.currentDisplay}"`, 'info');
                }
            });
            
            log('✅ Initialization complete. Click buttons above to test!', 'success');
        };
    </script>
</body>
</html>

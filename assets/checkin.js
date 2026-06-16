(function () {
    const badge = document.getElementById('statusBadge');
    const message = document.getElementById('statusMessage');
    const details = document.getElementById('studentDetails');
    let scanner = null;
    let scanningPaused = false;

    function setBadge(text, type) {
        badge.className = 'status-badge' + (type ? ' ' + type : '');
        badge.textContent = text;
    }

    function setDetails(rows) {
        details.innerHTML = '';

        rows.forEach(([label, value]) => {
            const wrapper = document.createElement('div');
            const term = document.createElement('dt');
            const description = document.createElement('dd');

            term.textContent = label;
            description.textContent = value || '-';
            wrapper.append(term, description);
            details.appendChild(wrapper);
        });
    }

    function extractToken(decodedText) {
        try {
            const url = new URL(decodedText);
            return url.searchParams.get('token') || decodedText;
        } catch (error) {
            return decodedText;
        }
    }

    async function verifyQr(decodedText) {
        if (scanningPaused) {
            return;
        }

        scanningPaused = true;
        setBadge('Checking Pass', '');
        message.textContent = 'Verifying student registration...';
        setDetails([]);

        const form = new FormData();
        form.append('qr', extractToken(decodedText));

        try {
            const response = await fetch('verify.php', {
                method: 'POST',
                body: form,
                headers: {
                    Accept: 'application/json',
                },
            });
            const result = await response.json();

            if (result.status === 'valid') {
                setBadge('Access Granted', 'success');
            } else if (result.status === 'already_checked_in') {
                setBadge('Already Checked In', 'warning');
            } else {
                setBadge('Invalid QR Code', 'danger');
            }

            message.textContent = result.message;

            if (result.student) {
                setDetails([
                    ['Name', result.student.student_name],
                    ['Class', result.student.class_name],
                    ['Admission No', result.student.admission_no],
                    ['Event', result.student.event_name],
                    ['Check-in Time', result.student.checkin_time],
                ]);
            }
        } catch (error) {
            setBadge('Scanner Error', 'danger');
            message.textContent = 'The pass could not be verified. Check the network and try again.';
            setDetails([]);
        }

        window.setTimeout(() => {
            scanningPaused = false;
            setBadge('Waiting for QR Code', '');
            message.textContent = 'Point the camera at a student pass.';
            setDetails([]);
        }, 3000);
    }

    function startScanner() {
        if (!window.Html5QrcodeScanner) {
            setBadge('Scanner Unavailable', 'danger');
            message.textContent = 'The QR scanner library could not load.';
            return;
        }

        scanner = new Html5QrcodeScanner('reader', {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            rememberLastUsedCamera: true,
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
        }, false);

        scanner.render(verifyQr);
    }

    window.addEventListener('load', startScanner);
})();


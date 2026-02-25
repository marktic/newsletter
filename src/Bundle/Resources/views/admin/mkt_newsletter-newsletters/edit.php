<?php

use Marktic\Newsletter\Newsletters\Models\NewsletterNewsletter;

/** @var NewsletterNewsletter $item */
$grapesjsData = $item->getGrapesjsData() ?: '{}';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css"/>
    <style>
        body { margin: 0; }
        #gjs { height: calc(100vh - 120px); }
        .editor-actions { padding: 10px; background: #f5f5f5; border-bottom: 1px solid #ddd; }
        .editor-actions button { margin-right: 5px; }
    </style>
</head>
<body>
<div class="editor-actions">
    <button id="save-btn" class="btn btn-primary">Save</button>
    <button id="back-btn" class="btn btn-default" onclick="history.back()">Back</button>
    <span id="save-status" style="margin-left:10px; color:green;"></span>
</div>
<div id="gjs"></div>

<script src="https://unpkg.com/grapesjs"></script>
<script>
    var editor = grapesjs.init({
        container: '#gjs',
        height: '100%',
        storageManager: false,
        plugins: [],
        canvas: {
            styles: []
        }
    });

    var grapesjsData = <?= json_encode($grapesjsData) ?>;
    if (grapesjsData && grapesjsData !== '{}') {
        try {
            editor.loadProjectData(JSON.parse(grapesjsData));
        } catch (e) {
            var htmlContent = <?= json_encode($item->getContent() ?: '') ?>;
            if (htmlContent) {
                editor.setComponents(htmlContent);
            }
        }
    } else {
        var htmlContent = <?= json_encode($item->getContent() ?: '') ?>;
        if (htmlContent) {
            editor.setComponents(htmlContent);
        }
    }

    document.getElementById('save-btn').addEventListener('click', function () {
        var projectData = JSON.stringify(editor.getProjectData());
        var htmlContent = editor.getHtml();
        var cssContent = editor.getCss();
        var fullContent = '<style>' + cssContent + '</style>' + htmlContent;

        var formData = new FormData();
        formData.append('grapesjs_data', projectData);
        formData.append('content', fullContent);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        }).then(function (response) {
            if (response.ok) {
                document.getElementById('save-status').textContent = 'Saved!';
                setTimeout(function () {
                    document.getElementById('save-status').textContent = '';
                }, 2000);
            } else {
                document.getElementById('save-status').textContent = 'Error saving!';
                document.getElementById('save-status').style.color = 'red';
            }
        }).catch(function () {
            document.getElementById('save-status').textContent = 'Error saving!';
            document.getElementById('save-status').style.color = 'red';
        });
    });
</script>
</body>
</html>

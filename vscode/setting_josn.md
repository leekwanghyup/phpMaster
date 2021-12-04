## VS Code 터미널을 GitBash로 변경
```js
{
    "workbench.colorTheme": "Dracula Soft",
    "explorer.confirmDelete": false,
    "explorer.confirmDragAndDrop": false,
    "workbench.iconTheme": "material-icon-theme",
    "editor.wordWrap": "on",
    "php.validate.executablePath" : "C:/xampp/php/php.exe",
    "php.validate.run": "onType",
    "editor.minimap.enabled": false,
    "window.zoomLevel": 1,
    "phpserver.relativePath": "./",
    "editor.fontFamily": "JetBrains Mono, monospace,'Courier New',Consolas", 

    // 여기서 부터 추가 
    "terminal.integrated.profiles.windows": {
        "GitBash": {
            "path":["C:\\Program Files\\Git\\bin\\bash.exe"], // gitbahs가 있는경로 
            "icon":"terminal-bash"
        },
 
        "PowerShell": {
            "source": "PowerShell",
            "icon": "terminal-powershell"
        },
        "Command Prompt": {
            "path": [
                "${env:windir}\\Sysnative\\cmd.exe",
                "${env:windir}\\System32\\cmd.exe"
            ],
            "args": [],
            "icon": "terminal-cmd"
        },
    },
    "terminal.integrated.defaultProfile.windows": "GitBash",
}
```
# File System Watcher – How It Works

## 1. What I Built
I created a Symfony tool running inside Docker that:

- Uses the `inotifywait` program (installed in the container via `inotify-tools`) to watch a folder for new, changed, or deleted files  
- Sends each event into Symfony Messenger for processing  
- Has a core handler (`HandleFileEvent`) that picks a small “strategy” class to handle that file  
- Moves finished files into subfolders by type (e.g. `processed/jpg`, `processed/json`, etc.)

## 2. Why I Chose Hexagonal Architecture
- **Clear separation**: Ports (interfaces) live in `src/Core/Port`, adapters in `src/Infrastructure/Adapter`, and application logic in `src/Core/Application`  
- **Easy to swap**: I can replace an adapter (e.g. switch from one HTTP client to another) without touching business logic  
- **Plug-in strategies**: Adding support for a new file type means dropping in a new strategy class—no big central switch to edit

## 3. Key Decisions
- **Docker + `inotifywait` over PHP extension**  
  I didn’t want to wrestle with PECL inside Docker. Instead, I installed `inotify-tools` in the image and shell out to `inotifywait`.  
- **One class per file type**  
  Instead of one giant `if/else`, each strategy only handles one job (optimize images, POST JSON, append text, unzip, or fetch a meme).  
- **PHP attributes for wiring**  
  I used `#[AsTaggedItem('app.file_strategy')]` and `#[TaggedIterator]` so strategy wiring lives next to the class—no YAML drift.  
- **Flexible logging**  
  I injected the standard PSR-3 logger everywhere so all steps get logged to `var/log/dev.log`. We can customize Monolog channels (e.g. send info messages to Slack or Discord, or write to additional files) without changing code.

## 4. Development Phases
1. **Architecture design**: Defined ports/interfaces and decided on hexagonal structure  
2. **Docker setup**: Built a dev image with PHP, Composer, Symfony, and `inotify-tools`  
3. **Core implementation**:  
   - Created ports/interfaces in `src/Core/Port`  
   - Wrote `HandleFileEvent` in `src/Core/Application`  
4. **Strategies & adapters**:  
   - One strategy class per file type in `src/Infrastructure/Strategy`  
   - Corresponding adapter classes in `src/Infrastructure/Adapter`  
5. **Makefile**: Planned to add a Makefile with shortcut commands (`make watch`, `make test`, etc.) to simplify common operations  
6. **Current phase: Manual testing**  
   I’m manually creating, deleting, and modifying files to verify each pipeline step works, and inspecting `var/log/dev.log` and the `watched/processed` folders.

## 5. What’s Next
Once manual tests pass, I’ll add automated tests—both unit tests for individual strategies and functional tests that spin up the watcher in a temp directory and assert end-to-end behavior.

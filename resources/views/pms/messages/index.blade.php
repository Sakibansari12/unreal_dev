@extends('pms.layouts.app')
@section('content')
<style>
    /* message */
    .messages {
        height: 500px;
        overflow-y: auto;
    }
    /* loader */
    .loader {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        animation: spin 0.7s linear infinite;
        margin: auto;
    }

    @keyframes spin {
        100% { transform: rotate(360deg); }
    }
    /* file ui */
    .attachment-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 8px 0;
    }

    .file-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f1f1f1;
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
        position: relative;
    }

    .file-icon {
        width: 28px;
        height: 28px;
        background: #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .remove-file {
        cursor: pointer;
        color: red;
        font-weight: bold;
        margin-left: 6px;
    }
    /* loader */
    .send-btn {
        background: none;
        border: none;
        cursor: pointer;
        position: relative;
    }
    .loader {
        border: 2px solid #f3f3f3;
        border-top: 2px solid #3498db;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        animation: spin 0.7s linear infinite;
    }
    @keyframes spin {
        100% { transform: rotate(360deg); }
    }
</style>
    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">Unified Inbox</h1>
                    </div>
                </div>
            </div>
            <div class="chatContainer">
                <!-- Chat Container -->
                <div class="chat-container">

                    <!-- Threads -->
                    <div class="thread-list {{ !empty(request('thread_id')) ? 'hidden' : '' }}">
                        @if(!empty($threads))
                            @foreach($threads as $thread)
                                @if (is_array($thread) && isset($thread['CommunicationChannel']) && in_array($thread['CommunicationChannel'], ['Airbnb','Booking.com']))
                                    <div class="thread {{ !empty($thread['ID']) && $thread['ID'] == $threadId ? 'unread' : '' }}"
                                        onclick="window.location='{{ route('pms.unified-inbox', ['thread_id' => $thread['ID']]) }}'">
                                        <div class="row g-2 align-items-center flex-row-reverse">
                                            <div class="col-auto">
                                                <div class="otaLogo">
                                                    @php $channel = $thread['CommunicationChannel'] ?? ''; @endphp
                                                    @if ($channel === 'Airbnb')
                                                        <img src="https://www.theriver.asia/wp-content/uploads/2020/01/pngkey.com-airbnb-logo-png-605967.png" alt="OTA">
                                                    @elseif($channel === 'Booking.com')
                                                        <img src="https://cdn.brandfetch.io/id9mEmLNcV/w/400/h/400/theme/dark/icon.jpeg" alt="OTA">
                                                    @elseif($channel === 'MakeMyTrip')
                                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ5KtO6UO_scXnTXfXk99apcH4k6gnFPvTkwg&s" alt="OTA">
                                                    @else
                                                        <img src="https://via.placeholder.com/40" alt="OTA">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col">
                                                {{ $thread['RecipientName'] ?? '' }}
                                                <div class="date">
                                                    @if (!empty($thread['LastMessageDate']))
                                                        {{ \Carbon\Carbon::parse($thread['LastMessageDate'])->format('d M Y | h:i A') }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p>No threads found.</p>    
                        @endif
                    </div>

                    <!-- Chat Area -->
                    <div class="chat-area {{ !empty(request('thread_id')) ? 'active' : '' }}">
                        {{-- <button class="chat-back-btn" onclick="showThreads()">⬅ Back</button> --}}
                        {{-- <div class="messages">
                            <div id="topLoader" style="display:none; text-align:center; padding:10px;">
                                <small>Loading...</small>
                            </div>
                            @if(is_array($messages) && count($messages) > 0)
                                @foreach($messages as $msg)
                                    <div class="message {{ $msg['IsIncoming'] ? 'received' : 'sent' }}">
                                        {!! $msg['Body'] !!}
                                        <span class="time">
                                            {{ \Carbon\Carbon::parse($msg['CreateDate'])->format('d M Y | h:i A') }}
                                        </span>
                                    </div>
                                @endforeach
                            @else
                                <p>No messages found.</p>
                            @endif
                        </div> --}}
                        <button class="chat-back-btn" onclick="window.location='{{ route('pms.unified-inbox') }}'">⬅ Back</button>
                        <div class="messages">
                            <!-- Top Loader -->
                            <div id="topLoader" style="display:none; text-align:center; padding:10px;">
                                <small>Loading...</small>
                            </div>

                            @if(is_array($messages) && count($messages) > 0)
                                @foreach($messages as $msg)
                                    <div class="message {{ $msg['IsIncoming'] ? 'received' : 'sent' }}">
                                        {{-- Message Text --}}
                                        @if(!empty($msg['Body']))
                                            <div class="message-text">
                                                {!! $msg['Body'] !!}
                                            </div>
                                        @endif

                                        {{-- Attachments --}}
                                        @if(!empty($msg['Attachments']))
                                            <div class="attachments mt-2">
                                                @foreach($msg['Attachments'] as $attachment)
                                                    @php
                                                        $base64 = str_replace(["\r", "\n"], '', $attachment['Content']);
                                                        $ext = strtolower(ltrim($attachment['Extension'] ?? '', '.'));
                                                        $name = $attachment['Name'] ?? 'file';
                                                    @endphp
                                                    <div>
                                                        <a href="javascript:void(0);" onclick="openFile(`{{ $base64 }}`, '{{ $ext }}', '{{ $name }}')" style="text-decoration: underline; color: #0d6efd;">
                                                            {{ $attachment['Name'] ?? '' }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        {{-- Time --}}
                                        <span class="time">
                                            {{ \Carbon\Carbon::parse($msg['CreateDate'])->format('d M Y | h:i A') }}
                                        </span>

                                    </div>
                                @endforeach
                            @else
                                {{-- <p>No messages found.</p> --}}
                            @endif
                        </div>
                        @if(isset($threadId))
                            <form method="POST" action="{{ route('pms.messages.send', $threadId) }}" enctype="multipart/form-data">
                                @csrf
                                @if(session('error'))
                                    <div class="alert alert-danger alert-dismissible fade show mb-2 ms-2 me-2" >
                                        {{ session('error') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif

                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show mb-2 ms-2 me-2" >
                                        {{ session('success') }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                <div id="attachmentPreview" class="attachment-preview"></div>
                                <div class="chat-input-area">
                                    <input type="text" id="messageInput" name="message" placeholder="Type a message...">
                                    {{-- <button type="submit">Send</button> --}}
                                    <label for="fileInput" style="cursor:pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-paperclip" viewBox="0 0 16 16">
                                            <path d="M4.5 3a2.5 2.5 0 0 1 5 0v9a1.5 1.5 0 0 1-3 0V5a.5.5 0 0 1 1 0v7a.5.5 0 0 0 1 0V3a1.5 1.5 0 1 0-3 0v9a2.5 2.5 0 0 0 5 0V5a.5.5 0 0 1 1 0v7a3.5 3.5 0 1 1-7 0z"/>
                                        </svg>
                                    </label>
                                    <input type="file" name="attachment[]" id="fileInput" style="display:none;" multiple>
                                    <button type="submit" id="sendBtn" class="send-btn" disabled>
                                        <span id="sendIcon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                                class="bi bi-send-fill" viewBox="0 0 16 16">
                                                <path
                                                    d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471z" />
                                            </svg>
                                        </span>
                                        <span id="sendLoader" class="loader" style="display:none;"></span>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        // scroll to loader when page load
        document.addEventListener("DOMContentLoaded", function () {
            var messageBox = document.querySelector('.messages');
            if (messageBox) {
                messageBox.scrollTo({
                    top: messageBox.scrollHeight,
                    behavior: 'smooth'
                });
            }
        });
    
        let limit = 16;
        let loading = false;

        const messageBox = document.querySelector('.messages');
        const threadId = "{{ $threadId ?? '' }}";
        const loader = document.getElementById('topLoader');

        // messageBox.addEventListener('scroll', function () {

        //     if (messageBox.scrollTop === 0 && !loading) {

        //         loading = true;
        //         loader.style.display = "block";   // SHOW LOADER
        //         limit += 10;

        //         fetch("{{ url('/pms/messages/load') }}/" + threadId + "?limit=" + limit)
        //             .then(res => res.json())
        //             .then(data => {

        //                 messageBox.innerHTML = '';
        //                 messageBox.appendChild(loader); // loader shows up at top

        //                 data.forEach(msg => {

        //                     let div = document.createElement('div');
        //                     div.className = 'message ' + (msg.IsIncoming ? 'received' : 'sent');
        //                     div.innerHTML = msg.Body +
        //                         `<span class="time">${formatDate(msg.CreateDate)}</span>`;

        //                     messageBox.appendChild(div);
        //                 });

        //                 loader.style.display = "none"; // HIDE LOADER
        //                 loading = false;
        //             })
        //             .catch(() => {
        //                 loader.style.display = "none";
        //                 loading = false;
        //             });
        //     }
        // });
        messageBox.addEventListener('scroll', function () {
            if (messageBox.scrollTop === 0 && !loading) {
                loading = true;
                loader.style.display = "block";
                const previousHeight = messageBox.scrollHeight;
                limit += 10;
                fetch("{{ url('/pms/messages/load') }}/" + threadId + "?limit=" + limit)
                    .then(res => res.json())
                    .then(data => {

                        loader.style.display = "none";

                        // Remove old rendered messages
                        messageBox.querySelectorAll('.message').forEach(e => e.remove());

                        data.forEach(msg => {

                            let div = document.createElement('div');
                            div.className = 'message ' + (msg.IsIncoming ? 'received' : 'sent');

                            let html = '';

                            // Message Text
                            if (msg.Body) {
                                html += `<div class="message-text">${msg.Body}</div>`;
                            }

                            // Attachments
                            if (msg.Attachments && msg.Attachments.length > 0) {

                                html += `<div class="attachments mt-2">`;

                                msg.Attachments.forEach(att => {

                                    let base64 = (att.Content || '').replace(/(\r\n|\n|\r)/gm, "");
                                    let ext = (att.Extension || '').replace('.', '').toLowerCase();
                                    let name = att.Name || 'file';

                                    html += `
                                        <div>
                                            <a href="javascript:void(0);"
                                            onclick="openFile('${base64}', '${ext}', '${name}')"
                                            style="text-decoration: underline; color: #0d6efd;">
                                                ${name}
                                            </a>
                                        </div>
                                    `;
                                });

                                html += `</div>`;
                            }

                            // Time
                            html += `<span class="time">${formatDate(msg.CreateDate)}</span>`;

                            div.innerHTML = html;

                            messageBox.appendChild(div);
                        });

                        // Maintain scroll position
                        const newHeight = messageBox.scrollHeight;
                        messageBox.scrollTop = newHeight - previousHeight;

                        loading = false;
                    })
                    .catch(() => {
                        loader.style.display = "none";
                        loading = false;
                    });
            }
        });

        function formatDate(dateStr) {
            let d = new Date(dateStr);
            return d.toLocaleString();
        }

        document.querySelector("form").addEventListener("submit", function() {
            let btn = document.getElementById("sendBtn");
            let icon = document.getElementById("sendIcon");
            let loader = document.getElementById("sendLoader");
            btn.disabled = true;      // button disable
            icon.style.display = "none"; 
            loader.style.display = "inline-block"; 
        });
    </script>
    <script>
        let selectedFiles = [];
        const fileInput = document.getElementById('fileInput');
        const previewContainer = document.getElementById('attachmentPreview');
        fileInput.addEventListener('change', function(e) {
            Array.from(e.target.files).forEach(file => {
                selectedFiles.push(file);
            });
            renderPreview();
            updateFileInput();
        });

        function renderPreview() {

            previewContainer.innerHTML = '';
            selectedFiles.forEach((file, index) => {
                let extension = file.name.split('.').pop().toUpperCase();
                let div = document.createElement('div');
                div.classList.add('file-item');
                div.innerHTML = `
                    <div class="file-icon">${extension}</div>
                    <div>${file.name}</div>
                    <div class="remove-file" onclick="removeFile(${index})">×</div>
                `;
                previewContainer.appendChild(div);
            });
        }

        function removeFile(index) {
            selectedFiles.splice(index, 1);
            renderPreview();
            updateFileInput();
        }

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
        }

        // button disable when message is empty
        const messageInput = document.getElementById('messageInput');
        const sendBtn = document.getElementById('sendBtn');

        function checkSendButton() {
            const hasText = messageInput.value.trim().length > 0;
            const hasFiles = selectedFiles.length > 0;

            if (hasText || hasFiles) {
                sendBtn.disabled = false;
                sendBtn.style.opacity = "1";
                sendBtn.style.cursor = "pointer";
            } else {
                sendBtn.disabled = true;
                sendBtn.style.opacity = "0.5";
                sendBtn.style.cursor = "not-allowed";
            }
        }

        messageInput.addEventListener('input', checkSendButton);

        function updateFileInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;

            checkSendButton();
        }

        document.addEventListener("DOMContentLoaded", checkSendButton);

        // image show in new tab
        function openFile(base64Data, ext, fileName) {
            const byteCharacters = atob(base64Data);
            const byteNumbers = new Uint8Array(byteCharacters.length);

            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            let mime = 'application/octet-stream';
            if (ext) {
                if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
                    mime = 'image/' + (ext === 'jpg' ? 'jpeg' : ext);
                } else if (ext === 'pdf') {
                    mime = 'application/pdf';
                } else {
                    mime = 'application/' + ext;
                }
            }
            const blob = new Blob([byteNumbers], { type: mime });
            const blobUrl = URL.createObjectURL(blob);
            // If previewable type → open
            if (mime.startsWith('image/') || mime === 'application/pdf') {
                window.open(blobUrl, '_blank');
            } else {
                // Force download with name
                const a = document.createElement('a');
                a.href = blobUrl;
                a.download = fileName;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        }
    </script>
@endsection

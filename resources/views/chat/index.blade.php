@extends('layouts.app')

@php use App\Exceptions\AiProviderException; @endphp

@section('title', 'Asisten AI')
@push('head')
    <meta data-id-page-title data-title-id="Asisten AI" data-title-en="AI Assistant">
@endpush

@section('content')
    <h1 class="page-title reveal" data-id="Asisten AI" data-en="AI Assistant">Asisten AI</h1>
    <p class="page-subtitle reveal reveal-1"
       data-id="Tanya apa pun tentang risiko, premi, polis, atau produk asuransi Anda."
       data-en="Ask anything about your risk, premium, policies, or insurance products.">Tanya apa pun tentang risiko, premi, polis, atau produk asuransi Anda.</p>

    <div class="card reveal reveal-1" style="margin-top:16px">
        <div class="section-heading">
            <div>
                <h2 data-id="Percakapan" data-en="Conversation">Percakapan</h2>
                <p>
                    <span data-id="Mesin:" data-en="Engine:">Mesin:</span>
                    <strong id="engineLabel">{{ $aiEnabled ? $engineLabel : 'nonaktif' }}</strong>
                    <span id="modelLabel" style="color:var(--muted)" @unless ($aiEnabled && $modelLabel !== '') hidden @endunless>&middot; {{ $modelLabel }}</span>
                </p>
            </div>
            <span class="pill {{ $aiEnabled ? 'pill-green' : ($aiState === AiProviderException::QUOTA_EXHAUSTED ? 'pill-amber' : 'pill-blue') }}">
                @switch($aiState)
                    @case(AiProviderException::QUOTA_EXHAUSTED)
                        <span data-id="Kuota habis" data-en="Quota spent">Kuota habis</span>
                        @break
                    @case(AiProviderException::RATE_LIMITED)
                        <span data-id="AI sibuk" data-en="AI busy">AI sibuk</span>
                        @break
                    @case(AiProviderException::UNAVAILABLE)
                        <span data-id="AI gangguan" data-en="AI degraded">AI gangguan</span>
                        @break
                    @default
                        {{ $aiEnabled ? 'AI aktif' : 'AI nonaktif' }}
                @endswitch
            </span>
        </div>

        @unless ($aiEnabled)
            <div class="empty" style="margin-bottom:14px">
                @php
                    $banner = match (true) {
                        ! $aiConfigured => [
                            'id' => ['Asisten AI belum aktif', 'Kunci API asisten (Groq atau Gemini) belum dikonfigurasi di server. Hubungi administrator aplikasi.'],
                            'en' => ['The AI assistant is not active', 'The assistant API key (Groq or Gemini) is not configured on the server. Please contact the application administrator.'],
                        ],
                        $aiReason === AiProviderException::INVALID_CREDENTIALS => [
                            'id' => ['Asisten AI belum aktif', 'Kunci API asisten tidak valid. Hubungi administrator aplikasi.'],
                            'en' => ['The AI assistant is not active', 'The assistant API key is invalid. Please contact the application administrator.'],
                        ],
                        $aiReason === AiProviderException::MODEL_NOT_FOUND => [
                            'id' => ['Asisten AI belum aktif', 'Model AI yang dikonfigurasi tidak tersedia untuk kunci ini. Hubungi administrator aplikasi.'],
                            'en' => ['The AI assistant is not active', 'The configured AI model is not available for this key. Please contact the application administrator.'],
                        ],
                        $aiReason === AiProviderException::QUOTA_EXHAUSTED => [
                            'id' => ['Kuota AI hari ini sudah habis', 'Kuota harian gratis asisten sudah terpakai. Asisten aktif lagi setelah kuota direset (biasanya per hari), atau lebih cepat bila administrator memasang kuota tambahan. Jawaban di bawah tetap bisa dibaca.'],
                            'en' => ['Today\'s AI quota is used up', 'The free daily quota for the assistant has been used. The assistant returns once it resets (usually daily), or sooner if the administrator adds more quota. Your existing answers are still readable.'],
                        ],
                        $aiReason === AiProviderException::RATE_LIMITED => [
                            'id' => ['Asisten AI sedang sibuk', 'Batas permintaan asisten tercapai. Coba lagi dalam beberapa saat.'],
                            'en' => ['The AI assistant is busy', 'The assistant request limit has been reached. Please try again in a moment.'],
                        ],
                        $aiReason === AiProviderException::UNAVAILABLE => [
                            'id' => ['Layanan AI sedang gangguan', 'Penyedia AI melaporkan model sedang kelebihan beban. Ini biasanya sementara, coba lagi sebentar lagi.'],
                            'en' => ['The AI service is having trouble', 'The AI provider reports the model is overloaded. This is usually temporary — try again shortly.'],
                        ],
                        default => [
                            'id' => ['Asisten AI tidak dapat terhubung', 'Server tidak dapat menghubungi layanan AI. Periksa koneksi server atau coba lagi nanti.'],
                            'en' => ['The AI assistant cannot connect', 'The server could not reach the AI service. Check the server connection or try again later.'],
                        ],
                    };
                @endphp
                <strong data-id="{{ $banner['id'][0] }}" data-en="{{ $banner['en'][0] }}">{{ $banner['id'][0] }}</strong>
                <span data-id="{{ $banner['id'][1] }}" data-en="{{ $banner['en'][1] }}">{{ $banner['id'][1] }}</span>
            </div>
        @endunless

        {{-- â”€â”€ Saran pertanyaan â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div id="suggestions" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
            @foreach ($suggestions as $suggestion)
                <button type="button" class="btn btn-ghost btn-sm suggestion"
                        data-id="{{ $suggestion['id'] }}" data-en="{{ $suggestion['en'] }}">{{ $suggestion['id'] }}</button>
            @endforeach
        </div>

        {{-- â”€â”€ Message list â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <div id="chatLog" style="display:grid;gap:11px;max-height:460px;overflow-y:auto;padding:4px 2px">
            @forelse ($messages as $message)
                <div class="chat-row" style="display:flex;justify-content:{{ $message->role === 'user' ? 'flex-end' : 'flex-start' }}">
                    <div class="chat-bubble {{ $message->role === 'user' ? 'chat-user' : 'chat-assistant' }}"
                         data-message-id="{{ $message->id }}"
                         data-id="{{ e($message->body_id) }}" data-en="{{ e($message->body_en) }}">{{ $message->body_id }}</div>
                </div>
            @empty
                <div class="empty" id="chatEmpty">
                    <strong data-id="Mulai percakapan" data-en="Start a conversation">Mulai percakapan</strong>
                    <span data-id="Pilih salah satu saran di atas atau tulis pertanyaan Anda."
                          data-en="Pick one of the suggestions above or type your question.">Pilih salah satu saran di atas atau tulis pertanyaan Anda.</span>
                </div>
            @endforelse
        </div>

        {{-- â”€â”€ Formulir kirim â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€ --}}
        <form id="chatForm" style="display:flex;gap:9px;margin-top:14px;flex-wrap:wrap">
            @csrf
            <input type="text" id="chatInput" name="message" autocomplete="off" style="flex:1;min-width:200px;border:1px solid var(--line);border-radius:10px;padding:10px 12px;font-size:13px"
                   data-id-placeholder="Tulis pertanyaan Andaâ€¦" data-en-placeholder="Type your questionâ€¦" placeholder="Tulis pertanyaan Andaâ€¦">
            <button type="submit" id="chatSubmit" class="btn btn-primary"
                    data-id="Kirim" data-en="Send">Kirim</button>
        </form>
    </div>

    <style>
        .chat-bubble {
            max-width: 72%;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 12.8px;
            line-height: 1.55;
            white-space: pre-wrap;
        }
        .chat-user { background: linear-gradient(145deg, #4778f5, #2a4fd6); color: #fff; border-bottom-right-radius: 5px; }
        .chat-assistant { background: #f3f6fd; color: #35435a; border: 1px solid var(--line); border-bottom-left-radius: 5px; }
        .chat-typing { color: var(--muted); font-style: italic; }
        /* Formatted assistant answers: bubble no longer needs pre-wrap. */
        .chat-bubble.chat-md { white-space: normal; }
        .chat-md > div + div { margin-top: 5px; }
        .chat-md ul, .chat-md ol { margin: 5px 0 2px; padding-left: 20px; }
        .chat-md li { margin: 2px 0; }
        .chat-md strong { font-weight: 700; color: inherit; }
        .chat-md em { font-style: italic; }
        .chat-md code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 11.8px;
            background: #e8eefa;
            border: 1px solid #dbe4f6;
            border-radius: 5px;
            padding: 0 4px;
        }
    </style>

    @push('scripts')
    <script>
        (function () {
            const form = document.getElementById('chatForm');
            const input = document.getElementById('chatInput');
            const log = document.getElementById('chatLog');
            const submit = document.getElementById('chatSubmit');
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const endpoint = @json(route('chat.store'));

            const csrf = () => {
                const meta = document.querySelector('meta[name="csrf-token"]');
                if (meta && meta.getAttribute('content')) {
                    return meta.getAttribute('content');
                }
                const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
                return match ? decodeURIComponent(match[1]) : '';
            };

            const makeRow = (text, role, translations = {}) => {
                const row = document.createElement('div');
                row.className = 'chat-row';
                row.style.display = 'flex';
                row.style.justifyContent = role === 'user' ? 'flex-end' : 'flex-start';

                const bubble = document.createElement('div');
                bubble.className = 'chat-bubble ' + (role === 'user' ? 'chat-user' : 'chat-assistant');
                bubble.textContent = text;

                if (translations.id) bubble.setAttribute('data-id', translations.id);
                if (translations.en) bubble.setAttribute('data-en', translations.en);

                row.appendChild(bubble);
                return { row, bubble };
            };

            const scrollDown = () => { log.scrollTop = log.scrollHeight; };

            // ── Safe inline formatting ─────────────────────────────────────
            // Gemini writes light Markdown (**bold**, *italic*, `code`, - lists,
            // paragraphs). We never assign raw HTML: every node is created with
            // createElement/createTextNode, so model output cannot inject HTML,
            // markup, or event handlers.
            // Inline emphasis. `_` only opens at a word boundary so snake_case
            // identifiers such as file_polis_kamu.pdf survive untouched; `*`
            // needs no such guard.
            const INLINE = /(\*\*(?=\S)[\s\S]+?\S\*\*|__(?=\S)[\s\S]+?\S__|`[^`\n]+`|\*(?=\S)[^*\n]+?\S\*|(?<![\p{L}\p{N}])_(?=\S)[^_\n]*\S_(?![\p{L}\p{N}]))/gu;

            // Every element is filled with a text node, never parsed markup.
            const withText = (el, text) => {
                el.textContent = text;

                return el;
            };

            const appendInline = (parent, line) => {
                const pieces = line.split(INLINE);

                pieces.forEach((piece) => {
                    if (! piece) return;

                    // `split` reports non-matching segments as undefined, so a token
                    // is only a delimiter pair when it also matches the INLINE
                    // pattern completely (guards against word-splitting).
                    const isCode = /^`[^`]+`$/u.test(piece);
                    const isStrong = /^(\*\*[\s\S]+\*\*|__[\s\S]+__)$/u.test(piece) && piece.length > 4;
                    const isEm = ! isStrong && /^(\*[^*]+\*|_([^_\n]*\S)_)$/u.test(piece) && piece.length > 2;

                    if (isCode) {
                        parent.appendChild(withText(document.createElement('code'), piece.slice(1, -1)));
                    } else if (isStrong) {
                        parent.appendChild(withText(document.createElement('strong'), piece.slice(2, -2)));
                    } else if (isEm) {
                        parent.appendChild(withText(document.createElement('em'), piece.slice(1, -1)));
                    } else {
                        parent.appendChild(document.createTextNode(piece));
                    }
                });
            };

            const renderMarkdown = (container, text) => {
                container.textContent = '';
                container.classList.add('chat-md');

                const source = String(text || '').replace(/\r\n?/g, '\n');
                let list = null;

                // Nothing to render: an empty bubble must stay empty.
                if (! source.trim()) return;

                const lines = source.split('\n');

                const closeList = () => {
                    if (list) container.appendChild(list);
                    list = null;
                };

                lines.forEach((line) => {
                    // Strip block-level Markdown we render ourselves (#, >, ---).
                    const clean = line.replace(/^\s{0,3}#{1,6}\s+/, '')
                        .replace(/^\s{0,3}>\s?/, '')
                        .replace(/^\s{0,3}([-*_])(?:\s*\1){2,}\s*$/, '—');
                    const bullet = clean.match(/^\s*[-*•]\s+(.*)$/);
                    const numbered = clean.match(/^\s*\d+[.)]\s+(.*)$/);

                    if (bullet || numbered) {
                        const ordered = Boolean(numbered);
                        if (! list || (list.tagName === 'OL') !== ordered) {
                            closeList();
                            list = document.createElement(ordered ? 'ol' : 'ul');
                        }
                        const li = document.createElement('li');
                        appendInline(li, (bullet || numbered)[1]);
                        list.appendChild(li);
                        return;
                    }

                    closeList();

                    if (! clean.trim()) {
                        container.appendChild(document.createElement('br'));
                        return;
                    }

                    const p = document.createElement('div');
                    appendInline(p, clean);
                    container.appendChild(p);
                });

                closeList();
            };

            // Bubbles hold either plain text (user / notices) or formatted text.
            const setBubble = (bubble, text, formatted) => {
                if (formatted) {
                    renderMarkdown(bubble, text);
                } else {
                    bubble.classList.remove('chat-md');
                    bubble.textContent = text;
                }
            };

            // One in-flight request at a time: a double submit (Enter + click,
            // or a retry after a slow answer) must not create duplicate turns.
            let sending = false;

            const send = async (message) => {
                const text = (message || '').trim();
                if (!text || sending) return;

                sending = true;

                document.getElementById('chatEmpty')?.remove();

                const { row, bubble: userBubble } = makeRow(text, 'user', { id: text, en: text });
                log.appendChild(row);
                scrollDown();

                submit.disabled = true;
                input.disabled = true;
                submit.textContent = window.imString('sending');

                const { row: typingRow, bubble: typingBubble } = makeRow(
                    window.imString('thinking'), 'assistant'
                );
                typingBubble.classList.add('chat-typing');
                log.appendChild(typingRow);
                scrollDown();

                const fail = (notice) => {
                    typingBubble.classList.remove('chat-typing');
                    setBubble(typingBubble, notice, false);
                    // Restore the message to the input box so it can be resent easily.
                    if (! input.value) input.value = text;
                };

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf(),
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ message: text }),
                    });

                    if (response.status === 419) {
                        input.value = text;
                        fail(window.imString('sessionExpired'));
                        return;
                    }

                    if (response.status === 422) {
                        const payload = await response.json().catch(() => null);
                        const first = payload && payload.errors ? Object.values(payload.errors)[0]?.[0] : null;
                        fail(first || window.imString('error'));
                        return;
                    }

                    if (! response.ok) {
                        const payload = await response.json().catch(() => null);
                        const lang = window.imLang();
                        const reason = payload && payload.error ? payload.error.reason : null;
                        if (payload && payload.user && payload.user.id) {
                            userBubble.setAttribute('data-message-id', payload.user.id);
                        }

                        // Retrying a spent daily quota gains nothing and only
                        // confuses the user, so do not put the text back.
                        if (reason === 'quota_exhausted') {
                            input.value = '';
                        }

                        const notice = payload && payload.error
                            ? (lang === 'en' ? payload.error.body_en : payload.error.body_id)
                            : null;

                        fail(notice || window.imString('error'));

                        // Mark the bubble so the language toggle does not try to
                        // re-render the notice from a data-id/data-en pair.
                        typingBubble.setAttribute('data-error', '1');
                        return;
                    }

                    const data = await response.json();
                    const lang = window.imLang();
                    userBubble.setAttribute('data-message-id', data.user.id);
                    typingBubble.setAttribute('data-message-id', data.assistant.id);
                    typingBubble.classList.remove('chat-typing');
                    setBubble(
                        typingBubble,
                        lang === 'en' ? data.assistant.body_en : data.assistant.body_id,
                        true
                    );
                    typingBubble.setAttribute('data-id', data.assistant.body_id || '');
                    typingBubble.setAttribute('data-en', data.assistant.body_en || '');

                    if (data.assistant.engine) {
                        document.getElementById('engineLabel').textContent = data.assistant.engine;

                        // The model is only shown when the server actually sent
                        // it back, so the UI never claims a model it cannot prove.
                        const modelLabel = document.getElementById('modelLabel');
                        if (modelLabel && data.assistant.model) {
                            modelLabel.textContent = '· ' + data.assistant.model;
                        }
                    }
                } catch (error) {
                    fail(window.imString('error'));
                } finally {
                    sending = false;
                    submit.disabled = false;
                    input.disabled = false;
                    submit.textContent = window.imLang() === 'en' ? 'Send' : 'Kirim';
                    scrollDown();
                    input.focus();
                }
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                const message = input.value;
                input.value = '';
                send(message);
            });

            document.querySelectorAll('.suggestion').forEach((button) => {
                button.addEventListener('click', () => {
                    const lang = window.imLang();
                    send(lang === 'en' ? button.getAttribute('data-en') : button.getAttribute('data-id'));
                });
            });

            // Format assistant bubbles that were rendered on the server (and are
            // therefore still in the log) as soon as the script runs.
            document.querySelectorAll('.chat-assistant[data-id]:not([data-error])').forEach((bubble) => {
                if (! bubble.classList.contains('chat-md')) {
                    const text = window.imLang() === 'en'
                        ? (bubble.getAttribute('data-en') || bubble.getAttribute('data-id'))
                        : bubble.getAttribute('data-id');
                    setBubble(bubble, text, true);
                }
            });

            requestAnimationFrame(scrollDown);

            document.addEventListener('im:lang', () => {
                log.querySelectorAll('.chat-bubble').forEach((bubble) => {
                    if (bubble.hasAttribute('data-error')) return;
                    const text = window.imLang() === 'en' ? bubble.getAttribute('data-en') : bubble.getAttribute('data-id');
                    if (text !== null && text !== '') {
                        setBubble(bubble, text, bubble.classList.contains('chat-assistant'));
                    }
                });
            });
        })();
    </script>
    @endpush
@endsection

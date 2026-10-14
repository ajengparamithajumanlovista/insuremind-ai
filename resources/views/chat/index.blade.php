@extends('layouts.app')

@section('title', 'Asisten AI')

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
                    <strong id="engineLabel">{{ $aiEnabled ? 'OpenAI' : 'rule-based' }}</strong>
                </p>
            </div>
            <span class="pill {{ $aiEnabled ? 'pill-green' : 'pill-blue' }}">
                {{ $aiEnabled ? 'AI aktif' : 'Mode lokal' }}
            </span>
        </div>

        {{-- ── Saran pertanyaan ───────────────────────────── --}}
        <div id="suggestions" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
            @foreach ($suggestions as $suggestion)
                <button type="button" class="btn btn-ghost btn-sm suggestion"
                        data-id="{{ $suggestion['id'] }}" data-en="{{ $suggestion['en'] }}">{{ $suggestion['id'] }}</button>
            @endforeach
        </div>

        {{-- ── Daftar pesan ───────────────────────────────── --}}
        <div id="chatLog" style="display:grid;gap:11px;max-height:460px;overflow-y:auto;padding:4px 2px">
            @forelse ($messages->reverse() as $message)
                <div class="chat-row" style="display:flex;justify-content:{{ $message->role === 'user' ? 'flex-end' : 'flex-start' }}">
                    <div class="chat-bubble {{ $message->role === 'user' ? 'chat-user' : 'chat-assistant' }}"
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

        {{-- ── Formulir kirim ─────────────────────────────── --}}
        <form id="chatForm" style="display:flex;gap:9px;margin-top:14px;flex-wrap:wrap">
            @csrf
            <input type="text" id="chatInput" name="message" autocomplete="off" style="flex:1;min-width:200px;border:1px solid var(--line);border-radius:10px;padding:10px 12px;font-size:13px"
                   data-id-placeholder="Tulis pertanyaan Anda…" data-en-placeholder="Type your question…" placeholder="Tulis pertanyaan Anda…">
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

            const send = async (message) => {
                const text = (message || '').trim();
                if (!text) return;

                document.getElementById('chatEmpty')?.remove();

                const { row } = makeRow(text, 'user', { id: text, en: text });
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
                    typingBubble.textContent = notice;
                    // Kembalikan pesan ke kotak input agar mudah dikirim ulang.
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
                        fail(window.imString('error'));
                        return;
                    }

                    const data = await response.json();
                    const lang = window.imLang();
                    typingBubble.classList.remove('chat-typing');
                    typingBubble.textContent = lang === 'en' ? data.assistant.body_en : data.assistant.body_id;
                    typingBubble.setAttribute('data-id', data.assistant.body_id || '');
                    typingBubble.setAttribute('data-en', data.assistant.body_en || '');

                    if (data.assistant.engine) {
                        document.getElementById('engineLabel').textContent = data.assistant.engine;
                    }
                } catch (error) {
                    fail(window.imString('error'));
                } finally {
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

            scrollDown();

            document.addEventListener('im:lang', () => {
                log.querySelectorAll('.chat-bubble').forEach((bubble) => {
                    const text = window.imLang() === 'en' ? bubble.getAttribute('data-en') : bubble.getAttribute('data-id');
                    if (text !== null && text !== '') {
                        bubble.textContent = text;
                    }
                });
            });
        })();
    </script>
    @endpush
@endsection

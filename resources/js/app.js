import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const themeButtons = document.querySelectorAll('[data-theme-toggle]');

function applyTheme(theme) {
    const isDark = theme === 'dark';
    document.documentElement.dataset.theme = isDark ? 'dark' : 'light';

    themeButtons.forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
        button.setAttribute('aria-label', isDark ? 'Bật giao diện sáng' : 'Bật giao diện tối');
        const label = button.querySelector('[data-theme-label]');
        if (label) {
            label.textContent = isDark ? 'Giao diện sáng' : 'Giao diện tối';
        }
    });
}

applyTheme(document.documentElement.dataset.theme || 'light');

themeButtons.forEach((button) => {
    button.addEventListener('click', () => {
        const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);

        try {
            localStorage.setItem('forum-theme', nextTheme);
        } catch (error) {
            console.error('Không thể lưu lựa chọn giao diện.', error);
        }
    });
});

const authYeti = document.querySelector('[data-auth-yeti]');
const authEmail = document.querySelector('[data-auth-email]');

if (authYeti && authEmail) {
    const updateEmailExpression = () => {
        authYeti.classList.toggle('is-email-active', authEmail.value.length > 0);
    };

    const updatePasswordExpression = () => {
        const activePassword = document.activeElement?.closest?.('[data-auth-password]');
        const isPasswordInput = activePassword instanceof HTMLInputElement;
        const isVisible = isPasswordInput && activePassword.type === 'text';

        authYeti.classList.toggle('is-password-focused', isPasswordInput && !isVisible);
        authYeti.classList.toggle('is-password-visible', isVisible);
    };

    authEmail.addEventListener('input', updateEmailExpression);
    updateEmailExpression();

    document.querySelectorAll('[data-auth-password]').forEach((passwordInput) => {
        passwordInput.addEventListener('focus', updatePasswordExpression);
        passwordInput.addEventListener('blur', updatePasswordExpression);
    });

    document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', () => {
            const passwordInput = toggle.closest('.auth-password-control')?.querySelector('[data-auth-password]');
            if (!(passwordInput instanceof HTMLInputElement)) {
                return;
            }

            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            toggle.textContent = showPassword ? 'Ẩn' : 'Hiện';
            toggle.setAttribute('aria-label', showPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            toggle.setAttribute('aria-pressed', String(showPassword));
            passwordInput.focus();
            updatePasswordExpression();
        });
    });
}

const editableInputTypes = new Set([
    'text',
    'search',
    'email',
    'url',
    'tel',
    'password',
    'number',
    'date',
    'datetime-local',
    'month',
    'week',
    'time',
]);
const writingCursor = document.createElement('img');
writingCursor.className = 'writing-cursor';
writingCursor.src = '/images/quill-cursor.png';
writingCursor.alt = '';
writingCursor.setAttribute('aria-hidden', 'true');
document.body.appendChild(writingCursor);

function getEditableField(target) {
    if (!(target instanceof Element)) {
        return null;
    }

    const field = target.closest('input, textarea, [contenteditable]');
    if (!field || field.disabled || field.readOnly) {
        return null;
    }

    if (field instanceof HTMLInputElement && !editableInputTypes.has(field.type)) {
        return null;
    }

    if (field.hasAttribute('contenteditable') && !field.isContentEditable) {
        return null;
    }

    return field;
}

function hideWritingCursor() {
    writingCursor.classList.remove('is-visible');
    document.documentElement.classList.remove('writing-cursor-active');
}

document.addEventListener('pointermove', (event) => {
    const field = event.pointerType === 'touch' ? null : getEditableField(event.target);
    if (!field) {
        hideWritingCursor();
        return;
    }

    document.documentElement.classList.add('writing-cursor-active');
    writingCursor.classList.add('is-visible');
    writingCursor.style.transform = `translate3d(${event.clientX - 2}px, ${event.clientY - 39}px, 0)`;
});

document.addEventListener('pointerout', (event) => {
    if (getEditableField(event.target) && !getEditableField(event.relatedTarget)) {
        hideWritingCursor();
    }
});

document.addEventListener('pointercancel', hideWritingCursor);
window.addEventListener('blur', hideWritingCursor);

const questionReturnPositionKey = 'forum-question-return-position';

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) {
        return;
    }

    const link = event.target.closest('a[href]');
    if (!(link instanceof HTMLAnchorElement)) {
        return;
    }

    const targetUrl = new URL(link.href, window.location.href);
    if (targetUrl.origin !== window.location.origin || !/^\/questions\/\d+\/?$/.test(targetUrl.pathname)) {
        return;
    }

    try {
        sessionStorage.setItem(questionReturnPositionKey, JSON.stringify({
            href: window.location.href,
            scrollY: window.scrollY,
        }));
    } catch (error) {
        console.error('Không thể lưu vị trí bài viết.', error);
    }
});

window.addEventListener('pageshow', () => {
    let savedPosition;

    try {
        const storedPosition = sessionStorage.getItem(questionReturnPositionKey);
        if (!storedPosition) {
            return;
        }

        savedPosition = JSON.parse(storedPosition);
        if (savedPosition.href !== window.location.href || !Number.isFinite(savedPosition.scrollY)) {
            return;
        }

        sessionStorage.removeItem(questionReturnPositionKey);
    } catch (error) {
        console.error('Không thể khôi phục vị trí bài viết.', error);
        sessionStorage.removeItem(questionReturnPositionKey);
        return;
    }

    requestAnimationFrame(() => {
        requestAnimationFrame(() => window.scrollTo(0, savedPosition.scrollY));
    });
});

document.querySelectorAll('[data-history-back]').forEach((backLink) => {
    let referrerIsSameOrigin = false;

    try {
        referrerIsSameOrigin = Boolean(document.referrer) && new URL(document.referrer).origin === window.location.origin;
    } catch (error) {
        console.error('Không thể kiểm tra trang trước đó.', error);
    }

    backLink.addEventListener('click', (event) => {
        if (!referrerIsSameOrigin || window.history.length < 2) {
            return;
        }

        event.preventDefault();
        window.history.back();
    });
});

const imageViewer = document.createElement('div');
imageViewer.className = 'image-viewer';
imageViewer.hidden = true;
imageViewer.setAttribute('role', 'dialog');
imageViewer.setAttribute('aria-modal', 'true');
imageViewer.setAttribute('aria-label', 'Xem ảnh đính kèm');
imageViewer.tabIndex = -1;

const imageViewerPanel = document.createElement('div');
imageViewerPanel.className = 'image-viewer__panel';

const imageViewerToolbar = document.createElement('div');
imageViewerToolbar.className = 'image-viewer__toolbar';

const imageViewerBack = document.createElement('button');
imageViewerBack.className = 'image-viewer__button';
imageViewerBack.type = 'button';
imageViewerBack.textContent = '← Quay lại';

const imageViewerActions = document.createElement('div');
imageViewerActions.className = 'image-viewer__actions';

const imageViewerDownload = document.createElement('a');
imageViewerDownload.className = 'image-viewer__button';
imageViewerDownload.textContent = 'Tải ảnh xuống';

const imageViewerClose = document.createElement('button');
imageViewerClose.className = 'image-viewer__button image-viewer__close';
imageViewerClose.type = 'button';
imageViewerClose.setAttribute('aria-label', 'Đóng ảnh');
imageViewerClose.textContent = '×';

const imageViewerImage = document.createElement('img');
imageViewerImage.className = 'image-viewer__image';

imageViewerActions.append(imageViewerDownload, imageViewerClose);
imageViewerToolbar.append(imageViewerBack, imageViewerActions);
imageViewerPanel.append(imageViewerToolbar, imageViewerImage);
imageViewer.appendChild(imageViewerPanel);
document.body.appendChild(imageViewer);

let imageViewerReturnFocus = null;
let previousBodyOverflow = '';

function closeImageViewer() {
    if (imageViewer.hidden) {
        return;
    }

    imageViewer.hidden = true;
    imageViewerImage.removeAttribute('src');
    imageViewerDownload.removeAttribute('href');
    document.body.style.overflow = previousBodyOverflow;
    imageViewerReturnFocus?.focus();
    imageViewerReturnFocus = null;
}

document.addEventListener('click', (event) => {
    if (!(event.target instanceof Element)) {
        return;
    }

    const imageLink = event.target.closest('a[data-image-viewer]');
    if (imageLink instanceof HTMLAnchorElement) {
        const imageUrl = new URL(imageLink.href, window.location.href);
        if (imageUrl.origin !== window.location.origin) {
            return;
        }

        event.preventDefault();
        imageViewerReturnFocus = imageLink;
        imageViewerImage.src = imageUrl.href;
        imageViewerImage.alt = imageLink.querySelector('img')?.alt || 'Ảnh đính kèm';
        imageViewerDownload.href = imageUrl.href;
        imageViewerDownload.download = imageLink.dataset.imageDownload || imageUrl.pathname.split('/').pop() || 'anh';
        previousBodyOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        imageViewer.hidden = false;
        imageViewerClose.focus();
        return;
    }

    if (event.target === imageViewer || event.target === imageViewerBack || event.target === imageViewerClose) {
        closeImageViewer();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeImageViewer();
    }
});

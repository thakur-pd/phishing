<?php
$user  = getCurrentUser();
$links = getUserLinks($user['id']);

// Calculate stats
$total_captures = 0;
foreach ($links as $link) {
    $total_captures += $link['capture_count'];
}
?>

<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">Welcome back, <?= htmlspecialchars($user['username']) ?>!</h1>
            <p class="text-slate-500 font-medium">Here's what's happening with your links today.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i>
                Create New Link
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-5">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-link"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Links</p>
                <p class="text-2xl font-extrabold text-slate-900"><?= count($links) ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-bolt"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Captures</p>
                <p class="text-2xl font-extrabold text-slate-900"><?= $total_captures ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex items-center gap-5">
            <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Conversion Rate</p>
                <p class="text-2xl font-extrabold text-slate-900"><?= count($links) > 0 ? round(($total_captures / count($links)) * 100, 1) : 0 ?>%</p>
            </div>
        </div>
    </div>

    <!-- Links Section -->
    <div>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list-ul text-indigo-600"></i>
                Your Power Links
            </h2>
        </div>

        <?php if (empty($links)): ?>
            <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
                <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                    <i class="fas fa-link-slash"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-900 mb-1">No links created yet</h3>
                <p class="text-slate-500 mb-6">Start by creating your first intelligent tracking link.</p>
                <button onclick="document.getElementById('create-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 bg-indigo-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-indigo-700 transition-all">
                    <i class="fas fa-plus"></i>
                    Create My First Link
                </button>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <?php foreach ($links as $link): ?>
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-indigo-50/50 transition-all overflow-hidden group">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                                        <?php
                                        $icon = 'camera';
                                        if($link['tool_type'] == 'microphone') $icon = 'microphone';
                                        if($link['tool_type'] == 'location') $icon = 'location-dot';
                                        if($link['tool_type'] == 'all') $icon = 'bolt';
                                        ?>
                                        <i class="fas fa-<?= $icon ?>"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors"><?= htmlspecialchars($link['title']) ?></h3>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-tighter"><?= htmlspecialchars($link['tool_type']) ?> TOOL</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-extrabold rounded-full uppercase">Active</span>
                                </div>
                            </div>
                            
                            <!-- Copy Link Section - Redesigned -->
                            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-4 mb-6 border border-indigo-100 flex items-center justify-between gap-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Share Link</p>
                                    <p id="url-<?= $link['id'] ?>" class="font-mono text-xs text-indigo-600 break-all truncate"><?= (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/index.php?link=" . $link['link_code'] ?></p>
                                </div>
                                <button onclick="copyToClipboard('url-<?= $link['id'] ?>')" class="flex-shrink-0 w-12 h-12 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-all flex items-center justify-center shadow-lg hover:shadow-indigo-200" title="Copy URL">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-lg font-bold text-slate-900"><?= $link['capture_count'] ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Captures</p>
                                </div>
                                <div class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                    <p class="text-lg font-bold text-slate-900"><?= htmlspecialchars($link['theme']) ?></p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">Theme</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="?page=view&id=<?= $link['id'] ?>" class="flex-1 flex items-center justify-center gap-2 bg-indigo-600 text-white py-3 rounded-2xl font-bold text-sm hover:bg-indigo-700 transition-all">
                                    <i class="fas fa-eye"></i>
                                    View Data
                                </a>
                                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this link?')" class="flex-shrink-0">
                                    <input type="hidden" name="action" value="delete_link">
                                    <input type="hidden" name="link_id" value="<?= $link['id'] ?>">
                                    <button type="submit" class="w-12 h-12 flex items-center justify-center bg-rose-50 text-rose-600 rounded-2xl font-bold hover:bg-rose-600 hover:text-white transition-all">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Link Modal -->
<div id="create-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="document.getElementById('create-modal').classList.add('hidden')"></div>
        <div class="relative bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-in zoom-in duration-300">
            <div class="p-8 md:p-10">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900">✨ Create Power Link</h2>
                        <p class="text-slate-500 font-medium">Configure your advanced tracking link below.</p>
                    </div>
                    <button onclick="document.getElementById('create-modal').classList.add('hidden')" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-full hover:bg-slate-100 transition-all">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="action" value="create_link">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Collection Tool</label>
                            <select name="tool_type" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                                <option value="camera">📷 Camera Capture</option>
                                <option value="microphone">🎤 Audio Record</option>
                                <option value="location">📍 GPS Tracking</option>
                                <option value="all">⚡ All-in-One Intelligence</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Redirect URL</label>
                            <input type="url" name="redirect_url" placeholder="https://example.com" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Link Title (Social Preview)</label>
                        <input type="text" name="title" placeholder="Enter link title" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Enter link description" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all" required></textarea>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Theme</label>
                        <select name="theme" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                            <option value="default">Default</option>
                            <option value="tiktok">TikTok</option>
                            <option value="instagram">Instagram</option>
                            <option value="youtube">YouTube</option>
                            <option value="google">Google Drive</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div id="mic-duration-field" class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Mic Duration (sec)</label>
                            <input type="number" name="mic_duration" min="1" max="60" value="5" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>
                        <div id="image-count-field" class="space-y-2">
                            <label class="text-sm font-bold text-slate-700 ml-1">Image Count</label>
                            <input type="number" name="image_count" min="1" max="20" value="5" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Cover Image (Optional)</label>
                        <input type="file" name="link_image" accept="image/*" class="w-full bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                    </div>

                    <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-extrabold text-lg shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-rocket"></i>
                        Launch Power Link
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Handle tool type change to show/hide mic duration and image count fields
function updateFieldVisibility() {
    const toolType = document.querySelector('select[name="tool_type"]').value;
    const micField = document.getElementById('mic-duration-field');
    const imageField = document.getElementById('image-count-field');
    
    if (toolType === 'microphone' || toolType === 'all') {
        micField.style.display = 'block';
    } else {
        micField.style.display = 'none';
    }
    
    if (toolType === 'camera' || toolType === 'all') {
        imageField.style.display = 'block';
    } else {
        imageField.style.display = 'none';
    }
}

// Initialize visibility on page load
document.addEventListener('DOMContentLoaded', function() {
    updateFieldVisibility();
    document.querySelector('select[name="tool_type"]').addEventListener('change', updateFieldVisibility);
});

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    if (!element) return;
    
    const text = element.innerText;
    
    // Try modern clipboard API first
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showCopyNotification(elementId);
        }).catch(() => {
            // Fallback to old method
            copyToClipboardFallback(text, elementId);
        });
    } else {
        // Fallback for older browsers
        copyToClipboardFallback(text, elementId);
    }
}

function copyToClipboardFallback(text, elementId) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showCopyNotification(elementId);
    } catch (err) {
        alert('Failed to copy URL');
    }
    document.body.removeChild(textArea);
}

function showCopyNotification(elementId) {
    const button = document.querySelector(`button[onclick="copyToClipboard('${elementId}')"]`);
    if (button) {
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.style.backgroundColor = '#10b981';
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.style.backgroundColor = '';
        }, 2000);
    }
}
</script>

package io.github.adjsky.jodi.plugins.virtualkeyboard;

import android.view.View;
import android.webkit.WebView;
import androidx.annotation.NonNull;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsAnimationCompat;
import androidx.core.view.WindowInsetsCompat;
import com.getcapacitor.JSObject;
import com.getcapacitor.Plugin;
import com.getcapacitor.annotation.CapacitorPlugin;
import java.util.List;

@CapacitorPlugin(name = "VirtualKeyboard")
public class VirtualKeyboardPlugin extends Plugin {
    private final VirtualKeyboardState state = new VirtualKeyboardState();
    private View insetsView;
    private WebView webView;
    private View.OnLayoutChangeListener layoutListener;
    private final Runnable requestInsets = () -> {
        if (insetsView != null) {
            ViewCompat.requestApplyInsets(insetsView);
        }
    };

    @Override
    public void load() {
        getActivity().runOnUiThread(() -> {
            webView = getBridge().getWebView();
            insetsView = getActivity().findViewById(android.R.id.content);

            ViewCompat.setWindowInsetsAnimationCallback(
                insetsView,
                new WindowInsetsAnimationCompat.Callback(WindowInsetsAnimationCompat.Callback.DISPATCH_MODE_CONTINUE_ON_SUBTREE) {
                    @Override
                    public void onPrepare(@NonNull WindowInsetsAnimationCompat animation) {
                        if (isImeAnimation(animation)) {
                            state.beginAnimation(animation);
                        }
                    }

                    @NonNull
                    @Override
                    public WindowInsetsCompat onProgress(
                        @NonNull WindowInsetsCompat insets,
                        @NonNull List<WindowInsetsAnimationCompat> runningAnimations
                    ) {
                        return withoutIme(insets);
                    }

                    @Override
                    public void onEnd(@NonNull WindowInsetsAnimationCompat animation) {
                        if (!isImeAnimation(animation)) {
                            return;
                        }

                        if (state.endAnimation(animation)) {
                            synchronizeHeight();
                        }
                    }
                }
            );

            ViewCompat.setOnApplyWindowInsetsListener(insetsView, (view, insets) -> {
                // Android dispatches the target insets before the first animation frame.
                updateHeight(insets);

                return withoutIme(insets);
            });

            layoutListener = (view, left, top, right, bottom, oldLeft, oldTop, oldRight, oldBottom) -> {
                synchronizeHeight();
            };
            webView.addOnLayoutChangeListener(layoutListener);
            insetsView.post(requestInsets);
        });
    }

    @Override
    protected void handleOnResume() {
        getActivity().runOnUiThread(() -> {
            state.clearAnimations();
            if (insetsView != null) {
                insetsView.post(requestInsets);
            }
        });
    }

    @Override
    protected void handleOnDestroy() {
        getActivity().runOnUiThread(() -> {
            if (insetsView != null) {
                insetsView.removeCallbacks(requestInsets);
                ViewCompat.setOnApplyWindowInsetsListener(insetsView, null);
                ViewCompat.setWindowInsetsAnimationCallback(insetsView, null);
            }
            if (webView != null && layoutListener != null) {
                webView.removeOnLayoutChangeListener(layoutListener);
            }
            state.clearAnimations();
            insetsView = null;
            webView = null;
        });
    }

    private boolean isImeAnimation(WindowInsetsAnimationCompat animation) {
        return (animation.getTypeMask() & WindowInsetsCompat.Type.ime()) != 0;
    }

    private WindowInsetsCompat withoutIme(WindowInsetsCompat insets) {
        return new WindowInsetsCompat.Builder(insets)
            .setInsets(WindowInsetsCompat.Type.ime(), Insets.NONE)
            .setVisible(WindowInsetsCompat.Type.ime(), false)
            .build();
    }

    private void synchronizeHeight() {
        if (insetsView == null || state.isAnimating()) {
            return;
        }

        WindowInsetsCompat insets = ViewCompat.getRootWindowInsets(insetsView);
        if (insets != null) {
            updateHeight(insets);
        }
    }

    private void updateHeight(WindowInsetsCompat insets) {
        if (webView == null || webView.getHeight() == 0) {
            return;
        }

        View decorView = getActivity().getWindow().getDecorView();
        int[] windowLocation = new int[2];
        int[] webViewLocation = new int[2];
        decorView.getLocationInWindow(windowLocation);
        webView.getLocationInWindow(webViewLocation);

        double height = VirtualKeyboardState.calculateHeight(
            windowLocation[1] + decorView.getHeight(),
            webViewLocation[1],
            webView.getHeight(),
            insets.getInsets(WindowInsetsCompat.Type.ime()).bottom,
            webView.getResources().getDisplayMetrics().density
        );

        if (!state.updateHeight(height)) {
            return;
        }

        notifyListeners("heightChange", snapshot());
    }

    private JSObject snapshot() {
        JSObject result = new JSObject();
        result.put("height", state.getHeight());
        return result;
    }
}

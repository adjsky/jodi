package dev.adjika.jodi.dev;

import android.webkit.WebView;

import com.getcapacitor.BridgeActivity;

public class MainActivity extends BridgeActivity {
    @Override
    public void onStart() {
        super.onStart();

        WebView webView = getBridge().getWebView();

        if (webView != null) {
            webView.setHapticFeedbackEnabled(false);
        }
    }
}

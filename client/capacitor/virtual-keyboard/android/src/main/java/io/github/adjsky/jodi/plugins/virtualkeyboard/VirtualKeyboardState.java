package io.github.adjsky.jodi.plugins.virtualkeyboard;

import java.util.Collections;
import java.util.IdentityHashMap;
import java.util.Set;

final class VirtualKeyboardState {
    private final Set<Object> animations = Collections.newSetFromMap(new IdentityHashMap<>());
    private double height;

    void beginAnimation(Object animation) {
        animations.add(animation);
    }

    boolean endAnimation(Object animation) {
        return animations.remove(animation) && animations.isEmpty();
    }

    void clearAnimations() {
        animations.clear();
    }

    boolean isAnimating() {
        return !animations.isEmpty();
    }

    double getHeight() {
        return height;
    }

    boolean updateHeight(double nextHeight) {
        if (Double.compare(height, nextHeight) == 0) {
            return false;
        }

        height = nextHeight;
        return true;
    }

    static double calculateHeight(int windowBottom, int webViewTop, int webViewHeight, int imeBottom, float density) {
        if (imeBottom <= 0 || webViewHeight <= 0 || density <= 0) {
            return 0;
        }

        int keyboardTop = windowBottom - imeBottom;
        int overlap = Math.max(0, webViewTop + webViewHeight - Math.max(webViewTop, keyboardTop));

        return Math.round(overlap / (double) density * 100) / 100.0;
    }
}

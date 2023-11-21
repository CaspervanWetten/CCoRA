import FeedbackCode from "src/feedback/feedback-code";

import Colors from "./colors";
import StyleManagerAction from "./actions/style-manager-action";
import CorrectEdgeStyle from "./actions/correct-edge-style";
import CorrectStateStyle from "./actions/correct-state-style";
import IncorrectEdgeStyle from "./actions/incorrect-edge-style";
import IncorrectStateStyle from "./actions/incorrect-state-style";
import WarningEdgeStyle from "./actions/warning-edge-style";
import WarningStateStyle from "./actions/warning-state-style";

export default class StyleManager {
    protected static callbacks: Map<FeedbackCode, StyleManagerAction> = null;

    public static setStyle(code: FeedbackCode, context: CanvasRenderingContext2D): void {
        if(StyleManager.callbacks === null) {
            StyleManager.fillCallbacks();
        }
        let action = StyleManager.callbacks.get(code);
        if(action) {
            action.exec(context);
        } else {
            throw new Error(`Could not set style for code ${code}`);
        }
    }

    public static setStateStandardStyle(context: CanvasRenderingContext2D): void {
        context.strokeStyle = "black";
        context.fillStyle = "white";
        context.lineWidth = 2;
        context.font = "16px monospace";
        context.textBaseline = "middle";
        context.textAlign = "center";
    }

    public static setStateSelectedStyle(context: CanvasRenderingContext2D): void {
        context.strokeStyle = Colors.blue;
        context.fillStyle = "white";
        context.lineWidth = 20;
    }

    public static setStateTextStyle(context: CanvasRenderingContext2D): void {
        context.fillStyle = "black";
    }

    public static setEdgeStandardStyle(context: CanvasRenderingContext2D): void {
        context.strokeStyle = "black";
        context.fillStyle = "black";
        context.lineWidth = 2;
        context.font = "16px mono";
        context.textBaseline = "middle";
        context.textAlign = "center";
    }

    public static setEdgeSelectedStyle(context: CanvasRenderingContext2D): void {
        context.strokeStyle = Colors.blue;
        context.lineWidth = 15;
    }

    public static setEdgeTextStyle(context: CanvasRenderingContext2D): void {
        context.fillStyle = "black";
    }

    protected static fillCallbacks(): void {
        let c = StyleManager.callbacks;
        if (c !== null) {
            return;
        }
        c = new Map();
        for(let code in FeedbackCode) {
            let fcode = Number(code) as FeedbackCode;
            let h = Math.floor(fcode / 100);
            let t = Math.floor((fcode - h * 100) / 10);
            if(Number(h)) {
                switch(h) {
                    case 2:
                        if (t == 2 || t == 3) {
                            c.set(fcode, new CorrectStateStyle());
                        } else if (t == 4 || t == 5) {
                            c.set(fcode, new CorrectEdgeStyle());
                        }
                        break;
                    case 3:
                        if (t == 2 || t == 3) {
                            c.set(fcode, new WarningStateStyle());
                        } else if (t == 4 || t == 5) {
                            c.set(fcode, new WarningEdgeStyle());
                        }
                        break;
                    case 4:
                        if (t == 2 || t == 3) {
                            c.set(fcode, new IncorrectStateStyle());
                        } else if (t == 4 || t == 5) {
                            c.set(fcode, new IncorrectEdgeStyle());
                        }
                        break;
                }
            }
        }
        StyleManager.callbacks = c;
    }
}

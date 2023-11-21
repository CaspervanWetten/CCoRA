import StyleManagerAction from "./style-manager-action";
import Colors from "../colors";

export default class WarningStateStyle implements StyleManagerAction {
    public exec(context: CanvasRenderingContext2D): void {
        context.strokeStyle = Colors.orange;
        context.lineWidth = 16;
    }
}

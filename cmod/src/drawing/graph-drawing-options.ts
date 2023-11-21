import Feedback from "src/feedback/feedback";
import { MarkingStringType } from "src/system/marking";

export default interface GraphDrawingOptions {
    selected: number | null;
    feedback: Feedback | null;
    markingStyle: MarkingStringType | null;
}

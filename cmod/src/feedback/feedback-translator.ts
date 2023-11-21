import FeedbackCode from "./feedback-code";

import HashTable from "src/hash-table/hash-table";

export default class FeedbackTranslator
{
    protected static translations: HashTable<FeedbackCode, string> | null = null;

    public static translate(code: FeedbackCode): string {
        if(FeedbackTranslator.translations == null) {
            FeedbackTranslator.fillTranslations();
        }
        if (FeedbackTranslator.translations!.has(code)) {
            return FeedbackTranslator.translations!.get(code)!;
        }
        throw new Error(`Could not retrieve translation for code ${code}`)
    }

    protected static fillTranslations(): void {
        FeedbackTranslator.translations = new HashTable<FeedbackCode, string>();

        let t = FeedbackTranslator.translations;
        
        // initial states
        t.set(FeedbackCode.NO_INITIAL_STATE, "No initial state is defined");
        t.set(FeedbackCode.INCORRECT_INITIAL_STATE, "The defined initial state is incorrect");
        t.set(FeedbackCode.CORRECT_INITIAL_STATE, "The initial state is correct");
        t.set(FeedbackCode.OMEGA_IN_INITIAL, "One of the places in the initial state is marked as unbounded. This is impossible");

        // states
        t.set(FeedbackCode.NOT_REACHABLE_FROM_PRESET, "This state is not reachable from one of the markings in its pre-set");
        t.set(FeedbackCode.REACHABLE_FROM_PRESET, "This state is reachable");
        t.set(FeedbackCode.EDGE_MISSING, "This state is missing an outgoing edge");
        t.set(FeedbackCode.DUPLICATE_STATE, "This state occurs multiple times in the graph");
        t.set(FeedbackCode.OMEGA_OMITTED, "One of the places can be marked unbounded");
        t.set(FeedbackCode.NOT_REACHABLE_FROM_INITIAL, "This state is not reachable from the initial state of the graph");
        t.set(FeedbackCode.OMEGA_FROM_PRESET_OMITTED, "One or more places does not have ω assigned to it, while at least one of the states in the preset does.");

        // edges
        t.set(FeedbackCode.ENABLED_CORRECT_POST, "This transition is enabled and points to the correct state");
        t.set(FeedbackCode.ENABLED_CORRECT_POST_WRONG_LABEL, "This edge's label is incorrect");
        t.set(FeedbackCode.ENABLED_INCORRECT_POST, "This transition does not lead to this state");
        t.set(FeedbackCode.DISABLED, "This transition can't fire");
        t.set(FeedbackCode.DISABLED_CORRECT_POST, "This transition is disabled, but this state is reachable");
        t.set(FeedbackCode.DUPLICATE_EDGE, "This transition leads to multiple states");
        t.set(FeedbackCode.MISSED_SELF_LOOP, "This edge should points to its source");
    }
}

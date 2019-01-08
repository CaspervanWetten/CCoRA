/// <reference path='./index.ts'/>

/// <reference path='../../../../../vendor/Definitions/Hashtable.d.ts'/>

class FeedbackTranslator
{
    protected static Translations : IHashtable<FeedbackCode, string> | undefined;

    public static Translate(feedbackid : FeedbackCode)
    {
        if(FeedbackTranslator.Translations == null) {
            FeedbackTranslator.FillTranslations();
        }
        return FeedbackTranslator.Translations.get(feedbackid);
    }

    protected static FillTranslations()
    {
        FeedbackTranslator.Translations = new Hashtable();

        let t = FeedbackTranslator.Translations;
        
        // initial states
        t.put(FeedbackCode.NO_INITIAL_STATE, "No initial state is defined");
        t.put(FeedbackCode.INCORRECT_INITIAL_STATE, "The defined initial state is incorrect");
        t.put(FeedbackCode.CORRECT_INITIAL_STATE, "The initial state is correct");

        // states
        t.put(FeedbackCode.NOT_REACHABLE_FROM_PRESET, "This state is not reachable from one of the markings in its pre-set");
        t.put(FeedbackCode.REACHABLE_FROM_PRESET, "This state is reachable");
        t.put(FeedbackCode.EDGE_MISSING, "This state is missing an outgoing edge");
        t.put(FeedbackCode.DUPLICATE_STATE, "This state occurs multiple times in the graph");
        t.put(FeedbackCode.NOT_REACHABLE_FROM_INITIAL, "This state is not reachable from the initial state of the graph");

        // edges
        t.put(FeedbackCode.ENABLED_CORRECT_POST, "This transition is enabled and points to the correct state");
        t.put(FeedbackCode.ENABLED_CORRECT_POST_WRONG_LABEL, "This edge's label is incorrect");
        t.put(FeedbackCode.ENABLED_INCORRECT_POST, "This transition does not lead to this state");
        t.put(FeedbackCode.DISABLED, "This transition can't fire");
        t.put(FeedbackCode.DISABLED_CORRECT_POST, "This transition is disabled, but this state is reachable");
        t.put(FeedbackCode.DUPLICATE_EDGE, "This transition leads to multiple states");
        t.put(FeedbackCode.MISSED_SELF_LOOP, "This edge should points to its source");
    }
}

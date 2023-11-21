enum FeedbackCode {
    CORRECT_INITIAL_STATE            = 200,
    NO_INITIAL_STATE                 = 400,
    INCORRECT_INITIAL_STATE          = 401,
    OMEGA_IN_INITIAL                 = 402,

    REACHABLE_FROM_PRESET            = 220,
    DUPLICATE_STATE                  = 320,
    OMEGA_OMITTED                    = 321,
    NOT_REACHABLE_FROM_PRESET        = 420,
    EDGE_MISSING                     = 421,
    NOT_REACHABLE_FROM_INITIAL       = 422,
    OMEGA_FROM_PRESET_OMITTED        = 423,

    ENABLED_CORRECT_POST             = 240,
    DUPLICATE_EDGE                   = 340,
    ENABLED_CORRECT_POST_WRONG_LABEL = 440,
    ENABLED_INCORRECT_POST           = 441,
    DISABLED                         = 442,
    DISABLED_CORRECT_POST            = 443,
    MISSED_SELF_LOOP                 = 444,
}

export default FeedbackCode;

class UserCreatedResponse
{
    user_id : number;
}

class PetrinetCreatedResponse
{
    petrinet_id: number;
    marking_id: number;
}

class ErrorResponse
{
    error : string;
}

type PetrinetImageResponse = string;

class MarkedPetrinetResponse
{
    petrinet: PetrinetResponse;
    marking: MarkingResponse;
}

type MarkingResponse = Object;

type FlowResponse = Flow;

class PetrinetResponse
{
    places: string[];
    transitions: string[];
    flows: FlowResponse[];
}

class SessionResponse
{
    session_id : number;
}

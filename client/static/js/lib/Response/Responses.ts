class UserCreatedResponse
{
    id : number;
    selfUrl : string;
}

class PetrinetCreatedResponse
{
    petrinetId  : number;
    petrinetUrl : string;
}

class ErrorResponse
{
    error : string;
}

type PetrinetImageResponse = string;

class PetrinetResponse
{
    flows           : Flow[];
    initialMarking  : Object;
    places          : string[];
    transitions     : string[];
}

class SessionResponse
{
    session_id : number;
}